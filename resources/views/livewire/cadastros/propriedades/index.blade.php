<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\Propriedade;

use function Livewire\Volt\{state, layout, mount, uses, with, usesPagination};

usesPagination();

uses([Toast::class]);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
});

with(function () {
    $propriedades = Propriedade::query()->withTrashed()->orderBy('id', 'asc');

    return [
        'propriedades' => $propriedades->paginate(15),
    ];
});

$inactivePropriedade = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    try {
        Propriedade::find($id)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Ad::username(),
        ]);

        return $this->success('Propriedade inativada com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível inativar a propriedade.');
    }
};

$restorePropriedade = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    try {
        Propriedade::withTrashed()->find($id)->restore();

        return $this->success('Propriedade reativada com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível reativar a propriedade.');
    }
};

layout('layouts.app');
?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Propriedades Cadastradas</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR PROPRIEDADE" icon="o-plus"
                      link="{{ route('propriedades.create') }}" />
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Localização</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Ações</th>
            </tr>
            </thead>
            <tbody class="text-gray-800">
            @forelse ($propriedades as $propriedade)
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center">{{ $propriedade->id }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $propriedade->local }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ !$propriedade->deleted_at ? 'Ativa' : 'Inativa' }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <x-button class="btn-outline btn-sm" tooltip="Editar Propriedade." icon="o-pencil"
                                  link="{{ route('propriedades.update', ['id' => $propriedade->id]) }}" />

                        @if (!$propriedade->deleted_at)
                            <x-button tooltip="Inativar Propriedade." icon="o-trash" class="btn-error btn-sm text-white"
                                      wire:confirm="Deseja realmente inativar esta propriedade?"
                                      wire:click="inactivePropriedade({{ $propriedade->id }})" />
                        @else
                            <x-button class="btn-sm btn-success" icon="o-check" tooltip="Reativar Propriedade."
                                      wire:confirm="Deseja realmente reativar esta propriedade?"
                                      wire:click="restorePropriedade({{ $propriedade->id }})" />
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center " colspan="5">Não há propriedades cadastradas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $propriedades->links() }}
    </div>
</div>
