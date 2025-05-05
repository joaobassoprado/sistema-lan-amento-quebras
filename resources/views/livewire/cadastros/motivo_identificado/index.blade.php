<?php

use App\Models\NaoIdentificado;
use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use function Livewire\Volt\{state, layout, mount, uses, with, usesPagination};

usesPagination();
uses([Toast::class]);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
});

with(function () {
    $motivos = NaoIdentificado::query()->withTrashed()->orderBy('id', 'asc');

    return [
        'motivos' => $motivos->paginate(15),
    ];
});

$inactiveMotivo = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    try {
        NaoIdentificado::find($id)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Ad::username(),
        ]);

        return $this->success('Motivo inativado com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível inativar o motivo.');
    }
};

$restoreMotivo = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }

    try {
        NaoIdentificado::withTrashed()->find($id)->restore();

        return $this->success('Motivo reativado com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível reativar o motivo.');
    }
};

layout('layouts.app');
?>
<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Motivos Cadastrados</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR MOTIVO" icon="o-plus"
                      link="{{ route('motivo_identificado.create') }}"/>
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Descrição</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Ações</th>
            </tr>
            </thead>
            <tbody class="text-gray-800">
            @forelse ($motivos as $motivo)
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center">{{ $motivo->id }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $motivo->justificativa ?? '-' }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ !$motivo->deleted_at ? 'Ativo' : 'Inativo' }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <x-button class="btn-outline btn-sm" tooltip="Editar Motivo." icon="o-pencil"
                                  link="{{ route('motivo_identificado.update', ['id' => $motivo->id]) }}"/>

                        @if (!$motivo->deleted_at)
                            <x-button tooltip="Inativar Motivo." icon="o-trash" class="btn-error btn-sm text-white"
                                      wire:confirm="Deseja realmente inativar este motivo?"
                                      wire:click="inactiveMotivo({{ $motivo->id }})"/>
                        @else
                            <x-button class="btn-sm btn-success" icon="o-check" tooltip="Reativar Motivo."
                                      wire:confirm="Deseja realmente reativar este motivo?"
                                      wire:click="restoreMotivo({{ $motivo->id }})"/>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center " colspan="5">Não há motivos cadastrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $motivos->links() }}
    </div>
</div>
