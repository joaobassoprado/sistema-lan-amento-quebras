<?php

use App\Models\NaoDescontado;
use App\Models\NaoIdentificado;
use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use function Livewire\Volt\{state, layout, mount, uses, rules};

uses([Toast::class]);

state(['id'])->url();
state(['justificativa', 'registro']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    $this->registro = NaoIdentificado::withTrashed()->find($this->id);

    if (!$this->registro) {
        return redirect(route('motivo_identificado.index'));
    }

    $this->justificativa = $this->registro->justificativa ?? '';
});

rules([
    'justificativa' => ['required'],
])->messages([
    'justificativa.required' => 'O campo "Motivo" é obrigatório.',
]);

$update = function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    $data = $this->validate();

    try {
        $this->registro->update([
            'justificativa' => $data['justificativa'],
            'updated_by' => Ad::username(),
            'updated_at' => Carbon::now(),
        ]);

        $this->success('Motivo editado com sucesso!');

        return redirect(route('motivo_identificado.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível editar o motivo.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Editar Motivo</h1>
        </div>
        <form wire:submit.prevent="update">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Motivo:" wire:model="justificativa" icon="o-pencil"
                         placeholder="Descreva o motivo aqui"/>
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
