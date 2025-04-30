<?php

use App\Models\Infracao;
use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\Profile;

use function Livewire\Volt\{state, layout, mount, uses, rules};

uses([Toast::class]);

state(['id'])->url();

state(['responsaveis' => [], 'gravidades' => [], 'n_pontos' => [], 'statuses' => []]);

state(['infraction',  'cod', 'infracao', 'responsavel', 'orgao_atuador', 'art_ctb', 'pontos', 'gravidade', 'valor']);

mount(function () {
    $this->infraction = Infracao::withTrashed()->find($this->id);

    if (!$this->infraction) {
        return redirect(route('admin.users.index'));
    }
    $this->responsaveis = [['id' => 'Proprietario', 'name' => 'Proprietário'], ['id' => 'Condutor', 'name' => 'Condutor']];
    $this->gravidades = [['id' => 'Leve', 'name' => 'Leve'], ['id' => 'Media', 'name' => 'Media'], ['id' => 'Grave', 'name' => 'Grave'],  ['id' => 'Gravissima', 'name' => 'Gravissíma']];
    $this->n_pontos = [['id' => '3', 'name' => '3'], ['id' => '4', 'name' => '4'], ['id' => '5', 'name' => '5'], ['id' => '7', 'name' => '7']];
    $this->statuses = [['id' => 'Ativo', 'name' => 'Ativo'], ['id' => 'Inativo', 'name' => 'Inativo']];

    $this->cod = $this->infraction->cod;
    $this->infracao = $this->infraction->infracao;
    $this->responsavel = $this->infraction->responsavel;
    $this->orgao_atuador = $this->infraction->orgao_atuador;
    $this->art_ctb = $this->infraction->art_ctb;
    $this->pontos = $this->infraction->pontos;
    $this->gravidade = $this->infraction->gravidade;
    $this->valor = $this->infraction->valor;
});

rules([
    'cod' => ['required'],
    'infracao' => ['required'],
    'orgao_atuador' => ['required'],
    'art_ctb' => ['required'],
    'gravidade' => ['required'],
    'responsavel' => ['required'],
    'pontos' => ['required'],
    'valor' => ['required', 'min:0.01'],
])->messages([
    'cod.required' => 'Insira o código da infração.',
    'infracao.required' => 'Descreva a infração.',
    'orgao_atuador.required' => 'Insira o orgão atuador.',
    'gravidade.required' => 'Selecione a gravidade.',
    'responsavel.required' => 'Selecione um responsável.',
    'pontos.required' => 'Insira o valor da infração.',
    'valor.required' => 'Informe o valor da infração',
    'valor.min' => 'Valor da infração não pode ser vazio.',

]);
$update = function () {

    $data = $this->validate();

    try {
        $this->infraction->update([
            'cod' => $data['cod'],
            'infracao' => $data['infracao'],
            'orgao_atuador' => $data['orgao_atuador'],
            'art_ctb' => $data['art_ctb'],
            'gravidade' => $data['gravidade'],
            'responsavel' => $data['responsavel'],
            'pontos' => $data['pontos'],
            'valor' => $data['valor'],
            'updated_by' => Ad::username(),
            'updated_at' => Carbon::now(),
        ]);

        $this->success('Infração editado com sucesso!');

        return redirect(route('infracoes.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível editar infração.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Editar Infração - Código {{ $this->infraction->cod }}</h1>
        </div>
        <form wire:submit.prevent="update">
            @csrf
            <div class="grid grid-cols-2 gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input type="number" label="Código da Infração:" placeholder="12345..." wire:model="cod"/>
                <x-input label="Descrição da Infração:" placeholder="Digite a infração..." wire:model="infracao"/>
                <x-input label="Orgão Atuador:" placeholder="Digite o orgão atuador..." wire:model="orgao_atuador"/>
                <x-input label="Art. Ctb:" placeholder="Digite o Art. Ctb..." wire:model.lazy="art_ctb" />
                <x-select label="Gravidade da Infração:" :options="$this->gravidades" wire:model="gravidade"
                          placeholder="Selecione a gravidade" placeholder-value="0" />
                <x-select label="Quantidade de Pontos:" :options="$this->n_pontos" wire:model="pontos"
                          placeholder="Selecione os pontos" placeholder-value="0" />
                <x-select label="Responsável:" :options="$this->responsaveis" wire:model="responsavel"
                          placeholder="Selecione o responsável" placeholder-value="0" />
                <x-input label="Valor:" placeholder="Ex.: 150,00" prefix="R$" money
                         wire:model="valor"  />
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
