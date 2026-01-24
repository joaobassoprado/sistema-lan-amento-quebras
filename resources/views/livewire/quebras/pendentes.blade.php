<?php

use Livewire\Volt\Component;
use App\Models\Quebra;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public $quebraSelecionada = null;
    public $observacao = '';
    public $permitido = false;

    public function mount()
    {
        // Verificação CENTRAL de permissão
        $this->permitido = Gate::allows('admin.view-any');
    }

    /**
     * Lista de quebras (SÓ se permitido)
     */
    public function getQuebrasProperty()
    {
        if (!$this->permitido) {
            return collect(); // evita erro silencioso
        }

        return Quebra::with(['produto', 'funcionario'])
            ->where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selecionar($id)
    {
        if (!$this->permitido) {
            return;
        }

        $this->quebraSelecionada = $id;
        $this->observacao = '';
    }

    public function aprovar()
    {
        if (!$this->permitido || !$this->quebraSelecionada) {
            return;
        }

        $quebra = Quebra::findOrFail($this->quebraSelecionada);

        if ($quebra->status !== 'pendente') {
            return;
        }

        $quebra->update([
            'status' => 'aprovada',
            'observacao' => $this->observacao,
        ]);

        $this->reset(['quebraSelecionada', 'observacao']);

        session()->flash('success', 'Quebra aprovada com sucesso');
    }

    public function reprovar()
    {
        if (!$this->permitido || !$this->quebraSelecionada) {
            return;
        }

        $quebra = Quebra::findOrFail($this->quebraSelecionada);

        if ($quebra->status !== 'pendente') {
            return;
        }

        $quebra->update([
            'status' => 'reprovada',
            'observacao' => $this->observacao,
        ]);

        $this->reset(['quebraSelecionada', 'observacao']);

        session()->flash('success', 'Quebra reprovada');
    }
};
?>

{{-- =========================
| USUÁRIO SEM PERMISSÃO
========================= --}}
@if (!$permitido)
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-md p-6 rounded-xl bg-yellow-100 border border-yellow-300 text-yellow-900 text-center shadow">
            <h2 class="text-xl font-bold mb-3">Acesso restrito</h2>
            <p>Solicite acesso ao TI da sua unidade.</p>
        </div>
    </div>
@endif


{{-- =========================
| ADMINISTRADOR
========================= --}}
@if ($permitido)
    <div>
        <h1 class="text-2xl font-bold mb-6">Quebras Pendentes</h1>

        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-green-100 rounded text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Data</th>
                        <th class="p-3 text-left">Produto</th>
                        <th class="p-3 text-left">Qtd</th>
                        <th class="p-3 text-left">Setor</th>
                        <th class="p-3 text-left">Motivo</th>
                        <th class="p-3 text-left">Funcionário</th>
                        <th class="p-3 text-left">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->quebras as $q)
                        <tr class="border-t">
                            <td class="p-3">
                                {{ $q->data ? \Carbon\Carbon::parse($q->data)->format('d/m/Y') : $q->created_at->format('d/m/Y') }}
                            </td>
                            <td class="p-3">{{ $q->produto->nome ?? '-' }}</td>
                            <td class="p-3">{{ $q->quantidade }}</td>
                            <td class="p-3">{{ $q->setor }}</td>
                            <td class="p-3">{{ $q->motivo }}</td>
                            <td class="p-3">{{ $q->funcionario->nome ?? '-' }}</td>
                            <td class="p-3">
                                <button class="text-blue-600 underline" wire:click="selecionar({{ $q->id }})">
                                    Aprovar / Reprovar
                                </button>
                            </td>
                        </tr>

                        @if ($quebraSelecionada === $q->id)
                            <tr class="bg-gray-50">
                                <td colspan="7" class="p-4">
                                    <label class="block mb-2 font-medium">Observação</label>
                                    <textarea wire:model="observacao" class="w-full border p-2 rounded mb-3"></textarea>

                                    <button class="bg-green-600 text-white px-4 py-2 rounded mr-2" wire:click="aprovar">
                                        Aprovar
                                    </button>

                                    <button class="bg-red-600 text-white px-4 py-2 rounded" wire:click="reprovar">
                                        Reprovar
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500">
                                Nenhuma quebra pendente
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
