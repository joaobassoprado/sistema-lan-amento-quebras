<?php

use App\Models\Infracao;
use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;


use function Livewire\Volt\{state, layout, mount, uses, rules, usesPagination};

usesPagination();

uses([Toast::class]);

state(['responsaveis'=>[], 'gravidades'=>[], 'n_pontos'=>[], 'statuses'=>[]]);

state(['filters', 'filter', 'cod','infracao','responsavel','orgao_atuador','art_ctb','pontos','gravidade','valor']);


mount(function () {
    }
    $this->responsaveis = [['id' => 'Proprietario', 'name' => 'Proprietário'], ['id' => 'Condutor', 'name' => 'Condutor']];
    $this->gravidades = [['id' => 'Leve', 'name' => 'Leve'], ['id' => 'Media', 'name' => 'Media'], ['id' => 'Grave', 'name' => 'Grave'],  ['id' => 'Gravissima', 'name' => 'Gravissíma']];
    $this->n_pontos = [['id' => '3', 'name' => '3'], ['id' => '4', 'name' => '4'], ['id' => '5', 'name' => '5'], ['id' => '7', 'name' => '7']];
    $this->statuses = [['id' => 'Ativo', 'name' => 'Ativo'], ['id' => 'Inativo', 'name' => 'Inativo']];
});


rules([
    'cod' => ['required', 'unique:infracoes,cod'],
    'infracao' => ['required', 'unique:infracoes,infracao'],
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
    'cod.unique' => 'Código já cadastrado.',
    'infracao.unique' => 'Infração já cadastrada.',
]);

$save = function () {
    $data = $this->validate();

    try {
        Infracao::create([
            'cod' => $data['cod'],
            'infracao' => $data['infracao'],
            'orgao_atuador' => $data['orgao_atuador'],
            'art_ctb' => $data['art_ctb'],
            'responsavel' => $data['responsavel'],
            'gravidade' => $data['gravidade'],
            'valor' => $data['valor'],
            'pontos' => $data['pontos'],
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Infração criada com sucesso!');

        return redirect(route('infracoes.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível adicionar infração.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Cadastrar Infração</h1>
        </div>
        <form wire:submit.prevent="save">
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
