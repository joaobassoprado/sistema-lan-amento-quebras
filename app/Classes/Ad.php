<?php

namespace App\Classes;

use Illuminate\Support\Facades\Auth;

class Ad
{
    public static function username(): ?string
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        /**
         * 🔧 Tratamento para LDAP/AD
         * O AD retorna atributos como arrays (ex: ['Nome']). 
         * Se for um array, pegamos o primeiro índice.
         */
        if (is_array($user->name)) {
            return $user->name[0] ?? null;
        }

        return $user->name;
    }

    public static function unidade(): ?string
    {
        // AD removido → não existe unidade
        return null;
    }
}