<?php

use App\Models\Infracao;
use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;


use function Livewire\Volt\{state, layout, mount, uses, with, usesPagination};

usesPagination();

uses([Toast::class]);

state(['responsaveis'=>[], 'gravidades'=>[], 'n_pontos'=>[], 'statuses'=>[]]);

state(['filters', 'filter', 'cod','infracao','responsavel','orgao_atuador','art_ctb','pontos','gravidade','status']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.users.view-any')) {
        return redirect(route('errors.403'));
    }
    $this->responsaveis = [['id' => 'Proprietario', 'name' => 'Proprietário'], ['id' => 'Condutor', 'name' => 'Condutor']];
    $this->gravidades = [['id' => 'Leve', 'name' => 'Leve'], ['id' => 'Media', 'name' => 'Media'], ['id' => 'Grave', 'name' => 'Grave'],  ['id' => 'Gravissima', 'name' => 'Gravissíma']];
    $this->n_pontos = [['id' => '3', 'name' => '3'], ['id' => '4', 'name' => '4'], ['id' => '5', 'name' => '5'], ['id' => '7', 'name' => '7']];
    $this->statuses = [['id' => 'Ativo', 'name' => 'Ativo'], ['id' => 'Inativo', 'name' => 'Inativo']];

    $filters = Session::get('filters', []);

    $this->cod = $filters['cod'] ?? [];
    $this->infracao = $filters['infracao'] ?? null;
    $this->responsavel = $filters['responsavel'] ?? null;
    $this->orgao_atuador = $filters['orgao_atuador'] ?? null;
    $this->art_ctb = $filters['art_ctb'] ?? null;
    $this->pontos = $filters['pontos'] ?? null;
    $this->gravidade = $filters['gravidade'] ?? null;
    $this->status = $filters['status'] ?? null;

});

with(function () {
    $infracoes = Infracao::query()->withTrashed()->orderBy('cod', 'asc')
        ->when($this->cod, fn($query) => $query->where('cod', 'LIKE', "%{$this->cod}%"))
        ->when($this->infracao, fn($query) => $query->where('infracao', 'LIKE', "%{$this->infracao}%"))
        ->when($this->responsavel, fn($query) => $query->where('responsavel', $this->responsavel))
        ->when($this->orgao_atuador, fn($query) => $query->where('orgao_atuador', 'LIKE', "%{$this->orgao_atuador}%"))
        ->when($this->art_ctb, fn($query) => $query->where('art_ctb',  'LIKE', "%{$this->art_ctb}%"))
        ->when($this->pontos, fn($query) => $query->where('pontos', $this->pontos))
        ->when($this->gravidade, fn($query) => $query->where('gravidade', 'LIKE', "%{$this->gravidade}%"));

    return [
        'infracoes' => $infracoes->paginate(15),
    ];
});

$filtrar = function () {
    Session::put('filters', [
        'cod' => $this->cod,
        'infracao' => $this->infracao,
        'responsavel' => $this->responsavel,
        'orgao_atuador' => $this->orgao_atuador,
        'art_ctb' => $this->art_ctb,
        'pontos' => $this->pontos,
        'gravidade' => $this->gravidade,
        'status' => $this->status,

    ]);
};

$resetarFiltros = function() {
    Session::forget('filters');

    return $this->reset(['cod','infracao','responsavel','orgao_atuador','art_ctb','pontos','gravidade','status']);

};


$inactiveInfracao = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('admin.users.delete')) {
        return $this->error('Sem permissão para inativar infração.');
    }

    try {
        Infracao::find($id)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Ad::username(),
        ]);

        return $this->success('Infração inativado com sucesso');
    } catch (Exception $e) {
        dd($e->getMessage());
        return $this->error('Não foi possível inativar infração.');
    }
};

$restoreInfracao = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('admin.users.restore')) {
        return $this->error('Sem permissão para reativar infração.');
    }

    try {
        Infracao::withTrashed()->find($id)->restore();

        return $this->success('Infração reativado com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível reativar infração.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Infração Cadastradas</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR INFRAÇÃO" icon="o-plus"
                      link="{{ route('infracoes.create') }}"/>
        </div>

        <div class="grid grid-cols-5 gap-4 bg-gray-100 p-4 shadow rounded mt-2">
            <x-input type="number" label="Filtrar por código:" placeholder="12345..." wire:model="cod"/>
            <x-input label="Filtrar por infração:" placeholder="Digite a infração..." wire:model="infracao"/>
            <x-select label="Filtrar por responsável:" :options="$this->responsaveis" wire:model="responsavel"
                      placeholder="Selecione o responsável" placeholder-value="0" />
            <x-input label="Filtrar por orgão atuador:" placeholder="Digite o orgão atuador..." wire:model="orgao_atuador"/>
            <x-input label="Filtrar por Art. Ctb:" placeholder="Digite o Art. Ctb..." wire:model.lazy="art_ctb" />
            <x-select label="Filtrar por pontos:" :options="$this->n_pontos" wire:model="pontos"
                      placeholder="N° de pontos" placeholder-value="0" />
            <x-select label="Filtrar por gravidade:" :options="$this->gravidades" wire:model="gravidade"
                      placeholder="Selecione a gravidade" placeholder-value="0" />
            <x-select label="Filtrar por status:" :options="$this->statuses" wire:model="status"
                      placeholder="Selecione o status" placeholder-value="0" />
            <x-button class="btn-outline mt-7" icon="o-x-circle" label="LIMPAR FILTROS" wire:click="resetarFiltros" />
            <x-button class="btn-outline mt-7" icon="o-adjustments-horizontal" label="FILTRAR"
                      wire:click="filtrar" />
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">Código</th>
                <th class="py-2 px-4 border-b">Infração</th>
                <th class="py-2 px-4 border-b">Responsável</th>
                <th class="py-2 px-4 border-b">Valor</th>
                <th class="py-2 px-4 border-b">Orgão Atuador</th>
                <th class="py-2 px-4 border-b">Art. Ctb</th>
                <th class="py-2 px-4 border-b">Pontos</th>
                <th class="py-2 px-4 border-b">Gravidade</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Ações</th>
            </tr>
            </thead>
            <tbody class="text-gray-800">
            @forelse ($infracoes as $infracao)
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->cod }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->infracao }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->responsavel }}</td>
                    <td class="py-2 px-4 border-b text-center">R${{number_format($infracao->valor, 2, ',', '.')}}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->orgao_atuador }}</td>
                    <td class="py-2 px-4 border-b text-center"> {{ trim($infracao->art_ctb) !== '' ? $infracao->art_ctb : '-' }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->pontos }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $infracao->gravidade }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ !$infracao->deleted_at ? 'Ativo' : 'Inativo' }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <x-button class="btn-outline btn-sm" tooltip="Editar infração." icon="o-pencil"
                                  link="{{ route('infracoes.update', ['id' => $infracao->id]) }}"/>

                        @if (!$infracao->deleted_at)
                            <x-button tooltip="Inativar Infração." icon="o-trash" class="btn-error btn-sm text-white"
                                      wire:confirm="Deseja realmente inativar essa infração?"
                                      wire:click="inactiveInfracao({{ $infracao->id }})"/>
                        @else
                            <x-button class="btn-sm btn-success" icon="o-check" tooltip="Reativar Infração."
                                      wire:confirm="Deseja realmente reativar essa infração?"
                                      wire:click="restoreInfracao({{ $infracao->id }})"/>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center ">Não há infrações cadastrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $infracoes->links() }}
    </div>
</div>
