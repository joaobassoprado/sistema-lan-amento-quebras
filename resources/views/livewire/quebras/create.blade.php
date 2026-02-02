<?php

use Livewire\Volt\Component;
use App\Models\Produto;
use App\Models\Funcionario;
use App\Models\Quebra;
use Illuminate\Support\Facades\Cache;
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

    public $mostrarProdutos = false;
    public $mostrarFuncionarios = false;

    public $areasDisponiveis = ['AG', 'CAMARA FRIA', 'CARGA/DESCARGA ROTA', 'DEVOLUÇÃO', 'MARKETPLACE', 'RETORNO DE ROTA', 'PICKING', 'PNC', 'PULMÃO A', 'PULMÃO B', 'PULMÃO C', 'PULMÃO D', 'RED ZONE', 'REFUGO', 'SAROBA', 'TENDAS CONTINGÊNCIA'];

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
    }

    public function updatedBuscaProduto()
    {
        $this->produto_id = null;
        $this->mostrarProdutos = true;
        $termo = trim($this->buscaProduto);

        if (mb_strlen($termo) >= 3) {
            $this->produtos = Produto::on200()
                ->select('código as id', 'nome')
                ->where('nome', 'like', '%' . $termo . '%')
                ->orderBy('nome')
                ->limit(15)
                ->get()
                ->toArray();
        }
    }

    public function selecionarProduto($id, $nome)
    {
        $this->produto_id = $id;
        $this->buscaProduto = $nome;
        $this->mostrarProdutos = false;
    }

    public function updatedBuscaFuncionario()
    {
        $this->funcionario_id = null;
        $this->mostrarFuncionarios = true;
        $termo = trim($this->buscaFuncionario);

        if (mb_strlen($termo) >= 3) {
            $this->funcionarios = Funcionario::on200()
                ->select('codigo as id', 'nome')
                ->where('nome', 'like', '%' . $termo . '%')
                ->orderBy('nome')
                ->limit(15)
                ->get()
                ->toArray();
        }
    }

    public function selecionarFuncionario($id, $nome)
    {
        $this->funcionario_id = $id;
        $this->buscaFuncionario = $nome;
        $this->mostrarFuncionarios = false;
    }

    public function fecharListas()
    {
        $this->mostrarProdutos = false;
        $this->mostrarFuncionarios = false;
    }

    public function save()
    {
        $this->validate([
            'data' => 'required|date',
            'produto_id' => 'required',
            'funcionario_id' => 'required',
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

        $this->reset(['quantidade', 'setor', 'area', 'motivo', 'turno', 'observacao', 'produto_id', 'funcionario_id', 'buscaProduto', 'buscaFuncionario', 'produtos', 'funcionarios']);
        $this->data = now()->format('Y-m-d');

        // Dispara a mensagem de sucesso
        session()->flash('success', 'Lançamento realizado com sucesso!');
    }
};
?>

<div class="min-h-screen bg-gray-50 py-10 px-6" x-data="{ showToast: false }" @click.outside="$wire.fecharListas()">

    {{-- MENSAGEM DE SUCESSO FLUTUANTE --}}
    @if (session()->has('success'))
        <div x-init="showToast = true;
        setTimeout(() => showToast = false, 4000)" x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-[-20px]"
            class="fixed top-5 right-5 z-[200] flex items-center bg-green-600 text-white px-6 py-4 rounded-2xl shadow-2xl border-2 border-white/20">
            <x-icon name="o-check-circle" class="w-8 h-8 mr-3" />
            <div>
                <p class="font-black uppercase text-sm tracking-wide">Sucesso!</p>
                <p class="text-xs opacity-90">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="max-w-5xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">Lançar Quebra</h1>
            <div class="text-sm text-gray-500 font-medium bg-white px-4 py-2 rounded-full shadow-sm border">
                Perfil: <span
                    class="{{ Gate::allows('admin-view-any') ? 'text-green-600' : 'text-blue-600' }} font-bold uppercase">
                    {{ Gate::allows('admin-view-any') ? 'ADMINISTRADOR' : 'PADRÃO' }}
                </span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            {{-- FORMULÁRIO COM BLOQUEIO DE ENTER --}}
            <form wire:submit.prevent="save" onkeydown="return event.key != 'Enter';"
                class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                {{-- DATA --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Data</label>
                    <input type="date" wire:model="data"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold">
                </div>

                {{-- PRODUTO --}}
                <div class="relative">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Produto</label>
                    <div class="relative group">
                        <input type="text" wire:model.live.debounce.500ms="buscaProduto"
                            placeholder="Digite o nome do produto..."
                            class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-blue-500 pr-10 font-medium">

                        <div wire:loading wire:target="buscaProduto"
                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    @if ($mostrarProdutos)
                        <div
                            class="absolute z-[100] w-full bg-white border border-gray-200 rounded-xl mt-1 shadow-2xl max-h-60 overflow-y-auto p-1">
                            <div wire:loading wire:target="buscaProduto"
                                class="p-3 text-sm text-gray-500 italic uppercase">Buscando produto...</div>
                            @forelse ($produtos as $p)
                                <button type="button"
                                    x-on:click="$wire.selecionarProduto({{ $p['id'] }}, '{{ addslashes($p['nome']) }}')"
                                    class="w-full text-left px-4 py-2.5 hover:bg-blue-600 hover:text-white rounded-lg text-sm flex justify-between items-center group transition-all">
                                    <span class="truncate pr-2 font-medium">{{ $p['nome'] }}</span>
                                    <span
                                        class="text-[9px] font-black opacity-30 group-hover:opacity-100 uppercase">#{{ $p['id'] }}</span>
                                </button>
                            @empty
                                <div wire:loading.remove wire:target="buscaProduto" class="p-3 text-sm text-gray-400">
                                    Nenhum produto encontrado.</div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- QUANTIDADE --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Quantidade</label>
                    <input type="number" wire:model="quantidade" placeholder="0"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold text-lg">
                </div>

                {{-- SETOR --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Setor</label>
                    <select wire:model="setor"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 font-medium uppercase">
                        <option value="">Selecione...</option>
                        <option value="Puxada">Puxada</option>
                        <option value="Armazém">Armazém</option>
                        <option value="Entrega">Entrega</option>
                        <option value="Virginia">Virginia</option>
                    </select>
                </div>

                {{-- RESPONSÁVEL --}}
                <div class="relative">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Funcionário
                        Responsável</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.500ms="buscaFuncionario"
                            placeholder="Nome do responsável..."
                            class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-blue-500 pr-10 font-medium">

                        <div wire:loading wire:target="buscaFuncionario"
                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    @if ($mostrarFuncionarios)
                        <div
                            class="absolute z-[100] w-full bg-white border border-gray-200 rounded-xl mt-1 shadow-2xl max-h-60 overflow-y-auto p-1">
                            <div wire:loading wire:target="buscaFuncionario"
                                class="p-3 text-sm text-gray-500 italic uppercase">Buscando colaborador...</div>
                            @forelse ($funcionarios as $f)
                                <button type="button"
                                    x-on:click="$wire.selecionarFuncionario({{ $f['id'] }}, '{{ addslashes($f['nome']) }}')"
                                    class="w-full text-left px-4 py-2.5 hover:bg-blue-600 hover:text-white rounded-lg text-sm uppercase transition-all flex justify-between items-center group">
                                    <span class="truncate font-medium">{{ $f['nome'] }}</span>
                                    <span class="text-[9px] opacity-30 group-hover:opacity-100">ID:
                                        {{ $f['id'] }}</span>
                                </button>
                            @empty
                                <div wire:loading.remove wire:target="buscaFuncionario"
                                    class="p-3 text-sm text-gray-400">Nenhum funcionário encontrado.</div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- TURNO --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Turno</label>
                    <select wire:model="turno"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 font-medium uppercase">
                        <option value="">Selecione...</option>
                        <option value="Manhã">Manhã</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noite">Noite</option>
                    </select>
                </div>

                {{-- ÁREA --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Área</label>
                    <select wire:model="area"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 uppercase font-medium">
                        <option value="">Selecione...</option>
                        @foreach ($areasDisponiveis as $opcao)
                            <option value="{{ $opcao }}">{{ $opcao }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- MOTIVO --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Motivo</label>
                    <input type="text" wire:model="motivo" list="listaMotivos" placeholder="Ex: Queda de palete"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-blue-500 font-medium uppercase">
                    <datalist id="listaMotivos">
                        <option value="QUEDA DE PALETE">
                        <option value="AVARIA NO PICKING">
                        <option value="PRODUTO VENCIDO">
                    </datalist>
                </div>

                {{-- OBSERVAÇÃO --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1 ml-1">Observação</label>
                    <textarea wire:model="observacao" rows="2"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 resize-none focus:bg-white focus:ring-blue-500 shadow-inner uppercase"></textarea>
                </div>

                {{-- BOTÃO SALVAR --}}
                <div class="col-span-1 md:col-span-2 flex justify-end mt-4 border-t border-gray-100 pt-6">
                    <button type="submit"
                        class="flex items-center gap-2 px-12 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 active:scale-95 transition-all shadow-lg uppercase tracking-widest text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Salvar Quebra</span>
                        <span wire:loading wire:target="save">Processando...</span>
                        <x-icon name="o-paper-airplane" class="w-5 h-5" wire:loading.remove wire:target="save" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
