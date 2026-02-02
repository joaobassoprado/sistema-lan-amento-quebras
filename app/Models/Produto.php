<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    // Define a conexão padrão para que o relacionamento no Resumo funcione
    protected $connection = 'db200';
    
    protected $table = 'produtos';

    // Primary key conforme seu banco (com acento)
    protected $primaryKey = 'código';

    // Como é uma tabela externa, geralmente não possui os timestamps do Laravel
    public $timestamps = false;

    protected $fillable = [
        'código',
        'nome',
    ];

    /**
     * Helper para chamadas estáticas se precisar forçar a conexão
     */
    public static function on200()
    {
        return (new static)->setConnection('db200');
    }

    public function quebras()
    {
        return $this->hasMany(Quebra::class, 'produto_id', 'código');
    }
}