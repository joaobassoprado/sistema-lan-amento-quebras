<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'codigo_externo',
        'nome',
        'ativo',
    ];

    // Um produto pode ter várias quebras
    public function quebras()
    {
        return $this->hasMany(Quebra::class);
    }
}
