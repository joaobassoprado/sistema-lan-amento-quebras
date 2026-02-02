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
     * Relacionamento com Produto
     * * produto_id: chave estrangeira na tabela 'quebras'
     * código: chave primária na tabela 'produtos' (conforme seu banco de dados)
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id', 'código');
    }

    /**
     * Relacionamento com Funcionário
     * * funcionario_id: chave estrangeira na tabela 'quebras'
     * codigo: chave primária na tabela 'funcionarios'
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id', 'codigo');
    }

    /**
     * Usuário (AD) que lançou a quebra
     */
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}