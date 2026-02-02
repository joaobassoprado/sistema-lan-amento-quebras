<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User as LocalUser;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin-view-any', function ($user) {
            
            // Se o usuário vier do LDAP, buscamos o registro correspondente no MySQL
            if (!($user instanceof LocalUser)) {
                // O samaccountname[0] é o login (ex: pedro.techuk)
                $login = $user->samaccountname[0] ?? null;

                if (!$login) return false;

                $user = LocalUser::where('name', $login)->first();
            }

            // Se não encontrou o usuário no banco local ou ele não tem perfil vinculado
            if (!$user || !$user->profile) {
                return false;
            }

            // Verifica se o nome do perfil é ADMINISTRADOR
            return trim(strtoupper($user->profile->name)) === 'ADMINISTRADOR';
        });
    }
}