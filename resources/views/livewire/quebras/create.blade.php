<?php

use Livewire\Volt\Component;
use App\Models\Produto;
use App\Models\Funcionario;
use App\Models\Quebra;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public $data;
    public $quantidade;
    public $setor = '';
    public $area = '';
    public $motivo = '';
    public $turno = '';
    public $observacao = '';

    public $produto_id = null;
    public $funcionario_id = null;

    public $buscaProduto = '';
    public $buscaFuncionario = '';

    public $produtos = [];
    public $funcionarios = [];

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
    }

    /* =====================
     * PRODUTO
     * ===================== */
    public function updatedBuscaProduto()
    {
        $this->produto_id = null;

        if (strlen($this->buscaProduto) < 3) {
            $this->produtos = [];
            return;
        }

        $termo = trim($this->buscaProduto);

        $this->produtos = Produto::where('ativo', 1)
            ->where(function ($q) use ($termo) {
                $q->where('nome', 'like', "{$termo}%")->orWhere('codigo_externo', 'like', "{$termo}%");
            })
            ->orderBy('nome')
            ->limit(8)
            ->get();
    }

    public function selecionarProduto($id, $nome)
    {
        $this->produto_id = $id;
        $this->buscaProduto = $nome;
        $this->produtos = [];
    }

    /* =====================
     * FUNCIONÁRIO
     * ===================== */
    public function updatedBuscaFuncionario()
    {
        $this->funcionario_id = null;

        if (strlen($this->buscaFuncionario) < 3) {
            $this->funcionarios = [];
            return;
        }

        $termo = trim($this->buscaFuncionario);

        $this->funcionarios = Funcionario::where('ativo', 1)
            ->where(function ($q) use ($termo) {
                $q->where('nome', 'like', "{$termo}%")->orWhere('codigo_externo', 'like', "{$termo}%");
            })
            ->orderBy('nome')
            ->limit(8)
            ->get();
    }

    public function selecionarFuncionario($id, $nome)
    {
        $this->funcionario_id = $id;
        $this->buscaFuncionario = $nome;
        $this->funcionarios = [];
    }

    /* =====================
     * AÇÃO SOMENTE ADMIN (TESTE)
     * ===================== */
    public function acaoAdmin()
    {
        abort_if(Gate::denies('admin.view-any'), 403);

        session()->flash('success', 'Ação executada SOMENTE por ADMIN.');
    }

    /* =====================
     * SALVAR
     * ===================== */
    public function save()
    {
        $this->validate([
            'data' => 'required|date',
            'produto_id' => 'required|exists:produtos,id',
            'funcionario_id' => 'required|exists:funcionarios,id',
            'quantidade' => 'required|integer|min:1',
            'setor' => 'required|string',
            'area' => 'required|string',
            'motivo' => 'required|string',
            'turno' => 'required|string',
        ]);

        Quebra::create([
            'data' => $this->data,
            'produto_id' => $this->produto_id,
            'funcionario_id' => $this->funcionario_id,
            'quantidade' => $this->quantidade,
            'setor' => $this->setor,
            'area' => $this->area,
            'motivo' => $this->motivo,
            'turno' => $this->turno,
            'observacao' => $this->observacao,
            'status' => 'pendente',
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Quebra lançada com sucesso.');

        $this->reset(['quantidade', 'setor', 'area', 'motivo', 'turno', 'observacao', 'produto_id', 'funcionario_id', 'buscaProduto', 'buscaFuncionario']);

        $this->data = now()->format('Y-m-d');
    }
};
?>

<!-- VIEW -->
<div class="min-h-screen bg-gray-50 py-10 px-6">
    <div class="max-w-5xl mx-auto">

        <h1 class="text-3xl font-bold mb-8 text-gray-800">Lançar Quebra</h1>

        @if (session()->has('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200">
                {{ session('success') }}
            </div>
        @endif


        <div class="bg-white p-8 rounded-2xl shadow border border-gray-200">
            <form wire:submit.prevent="save" class="grid grid-cols-2 gap-6">

                <div>
                    <label class="text-sm font-medium">Data</label>
                    <input type="date" wire:model="data" class="w-full rounded-lg border-gray-300">
                    @error('data')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative">
                    <label class="text-sm font-medium">Produto</label>
                    <input type="text" wire:model.debounce.600ms="buscaProduto"
                        placeholder="Digite ao menos 3 letras" class="w-full rounded-lg border-gray-300">

                    @error('produto_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    @if ($produtos)
                        <div class="absolute z-30 w-full bg-white border rounded-lg mt-1 shadow">
                            @foreach ($produtos as $p)
                                <div wire:click="selecionarProduto({{ $p->id }}, '{{ addslashes($p->nome) }}')"
                                    class="px-3 py-2 cursor-pointer hover:bg-blue-50">
                                    {{ $p->nome }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium">Quantidade</label>
                    <input type="number" wire:model="quantidade" class="w-full rounded-lg border-gray-300">
                    @error('quantidade')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Setor</label>
                    <select wire:model="setor" class="w-full rounded-lg border-gray-300">
                        <option value="">Selecione</option>
                        <option>Puxada</option>
                        <option>Armazém</option>
                        <option>Entrega</option>
                        <option>Virginia</option>
                    </select>
                    @error('setor')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Área</label>
                    <input type="text" wire:model="area" class="w-full rounded-lg border-gray-300">
                    @error('area')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Motivo</label>
                    <select wire:model="motivo" class="w-full rounded-lg border-gray-300">
                        <option value="">Selecione</option>
                        <option>QUEBRA</option>
                        <option>AVARIA</option>
                        <option>FALTA</option>
                        <option>VENCIDO</option>
                    </select>
                    @error('motivo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative">
                    <label class="text-sm font-medium">Funcionário</label>
                    <input type="text" wire:model.debounce.600ms="buscaFuncionario"
                        placeholder="Digite ao menos 3 letras" class="w-full rounded-lg border-gray-300">

                    @error('funcionario_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    @if ($funcionarios)
                        <div class="absolute z-30 w-full bg-white border rounded-lg mt-1 shadow">
                            @foreach ($funcionarios as $f)
                                <div wire:click="selecionarFuncionario({{ $f->id }}, '{{ addslashes($f->nome) }}')"
                                    class="px-3 py-2 cursor-pointer hover:bg-blue-50">
                                    {{ $f->nome }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium">Turno</label>
                    <select wire:model="turno" class="w-full rounded-lg border-gray-300">
                        <option value="">Selecione</option>
                        <option>Manhã</option>
                        <option>Tarde</option>
                        <option>Noite</option>
                    </select>
                    @error('turno')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="text-sm font-medium">Observação</label>
                    <textarea wire:model="observacao" class="w-full rounded-lg border-gray-300"></textarea>
                </div>

                <div class="col-span-2 text-right">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Salvar Quebra
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
