<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'password',
        'nome_completo',
        'profile_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relacionamento com a tabela de Perfis
     * Crucial para o Gate: permite acessar $user->profile->name
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * Retorna o nome completo ou o login do usuário (Usado no Layout)
     */
    public function getName(): string
    {
        return $this->nome_completo ?: $this->name;
    }

    /**
     * Helper opcional para checar se é admin direto no código: $user->isAdmin()
     */
    public function isAdmin(): bool
    {
        return $this->profile && str_contains(strtoupper($this->profile->name), 'ADMINISTRADOR');
    }
}