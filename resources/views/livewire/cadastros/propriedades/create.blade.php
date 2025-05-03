<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\Propriedade;

use function Livewire\Volt\{state, layout, mount, uses, rules, usesPagination};

usesPagination();

uses([Toast::class]);

state(['local' => '']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
});

rules([
    'local' => ['required', 'unique:propriedades,local'],
])->messages([
    'local.required' => 'O campo "Local" é obrigatório.',
    'local.unique' => 'Esta propriedade já está cadastrada.',
]);

$save = function () {
    $data = $this->validate();

    try {
        Propriedade::create([
            'local' => $data['local'],
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Propriedade cadastrada com sucesso!');

        return redirect(route('propriedades.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível cadastrar a propriedade.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Cadastro de Propriedade</h1>
        </div>
        <form wire:submit.prevent="save">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Local da Propriedade:" wire:model="local" icon="o-map-pin" placeholder="Ex: Fazenda Santa Maria"/>
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
