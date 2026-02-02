<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Funcionario extends Model
{
    use HasFactory;

    // Define a conexão padrão para que o relacionamento no Resumo funcione
    protected $connection = 'db200';

    protected $table = 'funcionarios';
    
    protected $primaryKey = 'codigo';
    
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nome',
        'status',
    ];

    /**
     * Helper para chamadas estáticas
     */
    public static function on200()
    {
        return (new static)->setConnection('db200');
    }

    /**
     * Global Scope: Sempre traz apenas funcionários ATIVOS
     */
    protected static function booted()
    {
        static::addGlobalScope('ativos', function (Builder $query) {
            $query->where('status', 'Ativo');
        });
    }

    public function quebras()
    {
        return $this->hasMany(Quebra::class, 'funcionario_id', 'codigo');
    }
}