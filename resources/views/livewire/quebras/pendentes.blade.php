<?php

use Livewire\Volt\Component;
use App\Models\Quebra;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public $quebraSelecionada = null;
    public $observacao = '';

    public function getQuebrasProperty()
    {
        if (!Gate::allows('admin-view-any')) {
            return collect();
        }

        return Quebra::with(['produto', 'funcionario'])
            ->where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selecionar($id)
    {
        $this->quebraSelecionada = $id;
        $this->observacao = '';
    }

    public function aprovar()
    {
        if (!Gate::allows('admin-view-any')) {
            return;
        }

        $quebra = Quebra::findOrFail($this->quebraSelecionada);
        $quebra->update([
            'status' => 'aprovada',
            'observacao' => $this->observacao,
        ]);

        $this->reset(['quebraSelecionada', 'observacao']);
        session()->flash('success', 'Quebra aprovada com sucesso!');
    }

    public function reprovar()
    {
        if (!Gate::allows('admin-view-any')) {
            return;
        }

        $quebra = Quebra::findOrFail($this->quebraSelecionada);
        $quebra->update([
            'status' => 'reprovada',
            'observacao' => $this->observacao,
        ]);

        $this->reset(['quebraSelecionada', 'observacao']);
        session()->flash('success', 'Quebra reprovada.');
    }
}; ?>

<div class="min-h-screen bg-gray-50 py-10 px-6">
    <div class="max-w-6xl mx-auto">

        @if (!Gate::allows('admin-view-any'))
            {{-- MENSAGEM DE BLOQUEIO --}}
            <div class="bg-white p-12 rounded-3xl shadow-xl border-2 border-dashed border-gray-200 text-center mt-20">
                <div class="bg-amber-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="o-lock-closed" class="w-10 h-10 text-amber-600" />
                </div>
                <h2 class="text-2xl font-black text-gray-800 uppercase italic">Acesso Restrito</h2>
                <p class="text-gray-500 mt-2 text-lg font-medium">Seu perfil atual não possui permissão para aprovar
                    quebras.</p>
                <div class="mt-6 inline-block bg-gray-100 px-6 py-3 rounded-2xl border border-gray-200">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Contate o administrador do
                        sistema</p>
                </div>
            </div>
        @else
            {{-- CONTEÚDO PARA ADMIN --}}
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Aprovações Pendentes</h1>
                <div class="bg-blue-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase">Painel Admin
                </div>
            </div>

            @if (session()->has('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Data</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Produto</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400 text-center">Qtd</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Setor</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400">Responsável</th>
                            <th class="p-4 text-xs font-black uppercase text-gray-400 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($this->quebras as $q)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 text-sm font-bold text-gray-600">{{ date('d/m/Y', strtotime($q->data)) }}
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-black text-gray-800">{{ $q->produto->nome ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $q->motivo }}</div>
                                </td>
                                <td class="p-4 text-center font-black text-blue-600 text-lg">{{ $q->quantidade }}</td>
                                <td class="p-4 text-xs font-bold uppercase text-gray-500">{{ $q->setor }}</td>
                                <td class="p-4 text-xs font-bold uppercase text-gray-500">
                                    {{ $q->funcionario->nome ?? 'N/A' }}</td>
                                <td class="p-4 text-center">
                                    <button wire:click="selecionar({{ $q->id }})"
                                        class="bg-gray-900 text-white px-4 py-2 rounded-xl text-xs font-black hover:scale-105 transition-all uppercase tracking-tighter">
                                        Analisar
                                    </button>
                                </td>
                            </tr>

                            @if ($quebraSelecionada === $q->id)
                                <tr class="bg-blue-50/30">
                                    <td colspan="6" class="p-6">
                                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100">
                                            <label
                                                class="block text-[10px] font-black uppercase text-blue-600 mb-2">Observações
                                                da Decisão</label>
                                            <textarea wire:model="observacao" class="w-full rounded-xl border-gray-200 bg-gray-50 mb-4 focus:ring-blue-500 text-sm"
                                                rows="2" placeholder="Digite o motivo da aprovação ou reprovação..."></textarea>
                                            <div class="flex gap-3 justify-end">
                                                <button wire:click="reprovar"
                                                    class="px-6 py-2 bg-red-100 text-red-600 font-black rounded-xl text-xs uppercase hover:bg-red-200 transition-colors">Reprovar
                                                    Quebra</button>
                                                <button wire:click="aprovar"
                                                    class="px-6 py-2 bg-green-600 text-white font-black rounded-xl text-xs uppercase shadow-lg shadow-green-200 hover:bg-green-700 transition-colors">Aprovar
                                                    Registro</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center">
                                    <x-icon name="o-check-circle" class="w-12 h-12 text-gray-200 mx-auto mb-4" />
                                    <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Tudo em dia!
                                        Nenhuma pendência.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
