<?php

use App\Models\NaoIdentificado;
use App\Classes\Ad;
use Mary\Traits\Toast;


use function Livewire\Volt\{state, layout, mount, uses, rules, usesPagination};

usesPagination();

uses([Toast::class]);

state(['justificativa' => '']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
});

rules([
    'justificativa' => ['required', 'unique:nao_descontados,justificativa'],
])->messages([
    'justificativa.required' => 'O campo "Motivo" é obrigatório.',
    'justificativa.unique' => 'Este motivo já está cadastrado.',
]);

$save = function () {
    $data = $this->validate();

    try {
        NaoIdentificado::create([
            'justificativa' => $data['justificativa'],
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Motivo cadastrado com sucesso!');

        return redirect(route('motivo_identificado.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível cadastrar o motivo.');
    }
};

layout('layouts.app');
?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Cadastro de Motivo para não desconto</h1>
        </div>
        <form wire:submit.prevent="save">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Motivo:" wire:model="justificativa" icon="o-pencil"
                         placeholder="Descreva o motivo aqui"/>
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
