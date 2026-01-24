<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionarios';

    protected $fillable = [
        'codigo_externo',
        'nome',
        'setor',
        'ativo',
    ];

    // Um funcionário pode ter várias quebras
    public function quebras()
    {
        return $this->hasMany(Quebra::class);
    }
}
