<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NaoDescontado extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = ['justificativa', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];
}
