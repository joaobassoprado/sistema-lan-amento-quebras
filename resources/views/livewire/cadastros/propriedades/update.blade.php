<?php

use App\Models\Propriedade;
use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;


use function Livewire\Volt\{state, layout, mount, uses, rules};

uses([Toast::class]);

state(['id'])->url();
state(['local', 'propriedade']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    $this->propriedade = Propriedade::withTrashed()->find($this->id);

    if (!$this->propriedade) {
        return redirect(route('propriedades.index'));
    }

    $this->local = $this->propriedade->local ?? '';
});

rules([
    'local' => ['required'],
])->messages([
    'local.required' => 'O campo "Local" é obrigatório.',
]);

$update = function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    $data = $this->validate();

    try {
        $this->propriedade->update([
            'local' => $data['local'],
            'updated_by' => Ad::username(),
            'updated_at' => Carbon::now(),
        ]);

        $this->success('Usuário editado com sucesso!');

        return redirect(route('propriedades.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível editar usuário.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Cadastro de Propriedade</h1>
        </div>
        <form wire:submit.prevent="update">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Local da Propriedade:" wire:model="local" icon="o-map-pin"
                         placeholder="Ex: Fazenda Santa Maria"/>
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
