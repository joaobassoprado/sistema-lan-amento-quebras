<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Infracao extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'infracoes';

    protected $fillable = ['cod', 'infracao', 'responsavel', 'valor', 'orgao_atuador', 'art_ctb', 'pontos', 'gravidade'];

    public function multas()
    {
        return $this->hasMany(Multa::class, 'cod_infracao', 'cod');
    }
}
