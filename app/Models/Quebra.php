<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quebra extends Model
{
    use HasFactory;

    protected $table = 'quebras';

    protected $fillable = [
        'data',
        'produto_id',
        'funcionario_id',
        'quantidade',
        'setor',
        'area',
        'motivo',
        'turno',
        'observacao',
        'status',        // pendente | aprovada | reprovada
        'created_by',
    ];

    /**
     * Produto da quebra
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Funcionário responsável
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    /**
     * Usuário (AD) que lançou a quebra
     */
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
