<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public function getResumoProperty()
    {
        return DB::table('quebras')->join('produtos', 'produtos.id', '=', 'quebras.produto_id')->where('quebras.status', 'aprovada')->select('produtos.codigo_externo as produto', DB::raw('SUM(quebras.quantidade) as total_un'))->groupBy('produtos.codigo_externo')->orderBy('produtos.codigo_externo')->get();
    }
};
?>

<div>

    <h1 class="text-2xl font-bold mb-6">Resumo de Quebras</h1>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Produto</th>
                    <th class="p-3 text-center">UN</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($this->resumo as $r)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="p-3 text-gray-800">
                            {{ $r->produto }}
                        </td>

                        <td class="p-3 text-center font-semibold text-gray-900">
                            {{ $r->total_un }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="2" class="p-6 text-center text-gray-500">
                            Nenhuma quebra aprovada encontrada
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>

</div>
