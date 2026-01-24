use App\Models\Quebra;
use Illuminate\Http\Request;

public function store(Request $request)
{
    $request->validate([
        'produto_id' => 'required',
        'funcionario_id' => 'required',
        'quantidade' => 'required|integer|min:1',
        'setor' => 'required',
        'motivo' => 'required',
        'turno' => 'required',
    ]);

    $quebra = Quebra::create([
        'data' => now(),
        'produto_id' => $request->produto_id,
        'funcionario_id' => $request->funcionario_id,
        'quantidade' => $request->quantidade,
        'setor' => $request->setor,
        'area' => $request->area,
        'motivo' => $request->motivo,
        'turno' => $request->turno,
        'observacao' => $request->observacao,
        'status' => 'pendente',
        'created_by' => auth()->id(),
    ]);

    $quebra->aprovacao()->create([
        'status' => 'pendente',
    ]);

    return redirect()
        ->route('quebras.pendentes')
        ->with('success', 'Quebra lançada com sucesso!');
}
