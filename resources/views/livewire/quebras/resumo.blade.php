<?php

use Livewire\Volt\Component;
use App\Models\Quebra;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $dataInicio;
    public $dataFim;

    public function mount()
    {
        $this->dataInicio = now()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
    }

    public function with()
    {
        return [
            'quebras' => Quebra::with(['produto', 'funcionario'])
                ->whereBetween('data', [$this->dataInicio, $this->dataFim])
                ->latest()
                ->paginate(15),
        ];
    }
}; ?>

<div class="min-h-screen bg-gray-50 py-10 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Resumo de Quebras</h1>

            <div class="flex gap-4 bg-white p-2 rounded-2xl shadow-sm border">
                <input type="date" wire:model.live="dataInicio"
                    class="border-none bg-transparent text-sm font-bold focus:ring-0">
                <span class="text-gray-300 flex items-center">|</span>
                <input type="date" wire:model.live="dataFim"
                    class="border-none bg-transparent text-sm font-bold focus:ring-0">
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Data/Turno</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Produto/Motivo</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Local (Setor/Área)</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Qtd</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Responsável</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($quebras as $q)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                {{-- DATA E TURNO --}}
                                <td class="p-4 text-sm">
                                    <div class="font-bold text-gray-700">{{ date('d/m/Y', strtotime($q->data)) }}</div>
                                    <div class="text-[10px] text-blue-500 font-black uppercase">{{ $q->turno }}
                                    </div>
                                </td>

                                {{-- PRODUTO E MOTIVO --}}
                                <td class="p-4">
                                    <div class="text-sm font-black text-gray-800">{{ $q->produto->nome ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase font-medium">{{ $q->motivo }}
                                    </div>
                                </td>

                                {{-- SETOR E ÁREA --}}
                                <td class="p-4">
                                    <div class="text-xs font-bold text-gray-700 uppercase">{{ $q->setor }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase italic">{{ $q->area }}</div>
                                </td>

                                {{-- QUANTIDADE --}}
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center justify-center bg-blue-50 text-blue-700 px-3 py-1 rounded-lg font-black">
                                        {{ $q->quantidade }}
                                    </span>
                                </td>

                                {{-- RESPONSÁVEL --}}
                                <td class="p-4">
                                    <div class="text-xs font-bold uppercase text-gray-600">
                                        {{ $q->funcionario->nome ?? 'N/A' }}
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="p-4 text-center">
                                    @php
                                        $statusColor = match ($q->status) {
                                            'aprovada' => 'bg-green-100 text-green-700 border-green-200',
                                            'reprovada' => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-amber-100 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $statusColor }} border px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter">
                                        {{ $q->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($quebras->isEmpty())
                <div class="p-20 text-center">
                    <div class="text-gray-300 mb-2">
                        <x-icon name="o-beaker" class="w-12 h-12 mx-auto" />
                    </div>
                    <p class="text-gray-500 font-medium">Nenhuma quebra encontrada para este período.</p>
                </div>
            @endif

            <div class="p-6 border-t bg-gray-50/50">
                {{ $quebras->links() }}
            </div>
        </div>
    </div>
</div>
