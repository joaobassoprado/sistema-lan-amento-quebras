<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Livewire\Livewire;
use App\Models\Produto;
use App\Models\Funcionario;

// ---------------------------------------------------------------------
// CORREÇÃO PARA AMBIENTE DE PRODUÇÃO (IIS / SUBDIRETÓRIO)
// ---------------------------------------------------------------------
if (env('APP_ENV') === 'production') {
    Livewire::setScriptRoute(function ($handle) {
        return Route::get('quebras_V2/livewire/livewire.js', $handle);
    });

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('quebras_V2/livewire/update', $handle);
    });
}

// ---------------------------------------------------------------------
// ERROS
// ---------------------------------------------------------------------
Route::prefix('/erros')->group(function () {
    Route::view('/403', 'erros.403')->name('errors.403');
    Route::view('/401', 'erros.401')->name('errors.401');
});

// ---------------------------------------------------------------------
// ÁREA LOGADA (REQUER AUTENTICAÇÃO)
// ---------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirecionamento inicial
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard
    Volt::route('/dashboard', 'dashboard')->name('dashboard');

    // -----------------------------------------------------------------
    // MÓDULO DE QUEBRAS
    // -----------------------------------------------------------------
    Route::prefix('/quebras')->group(function () {

        // Acesso comum
        Volt::route('/lancar', 'quebras.create')
            ->name('quebras.create');

        Volt::route('/resumo', 'quebras.resumo')
            ->name('quebras.resumo');

        // 🔐 SOMENTE ADMIN
        Volt::route('/pendentes', 'quebras.pendentes')

            ->name('quebras.pendentes');
    });

    // -----------------------------------------------------------------
    // CONSULTAS
    // -----------------------------------------------------------------
    Route::prefix('/consultas')->group(function () {
        Volt::route('/', 'consultas.index')->name('consultas.index');
        Volt::route('/info/{id}', 'consultas.info')->name('consultas.info');
    });

    // -----------------------------------------------------------------
    // APIs DE BUSCA
    // -----------------------------------------------------------------
    Route::get('/api/produtos', function (Request $request) {
        $q = trim($request->get('q'));
        if (strlen($q) < 3) {
            return [];
        }

        return Produto::select('id', 'nome')
            ->where('ativo', 1)
            ->where(function ($query) use ($q) {
                $query->where('nome', 'like', "{$q}%")
                      ->orWhere('codigo_externo', 'like', "{$q}%");
            })
            ->orderBy('nome')
            ->limit(10)
            ->get();
    });

    Route::get('/api/funcionarios', function (Request $request) {
        $q = trim($request->get('q'));
        if (strlen($q) < 3) {
            return [];
        }

        return Funcionario::select('id', 'nome')
            ->where('ativo', 1)
            ->where(function ($query) use ($q) {
                $query->where('nome', 'like', "{$q}%")
                      ->orWhere('codigo_externo', 'like', "{$q}%");
            })
            ->orderBy('nome')
            ->limit(10)
            ->get();
    });

    // -----------------------------------------------------------------
    // ÁREA ADMINISTRATIVA
    // -----------------------------------------------------------------
    Route::prefix('/admin')->group(function () {

        // Exportações
        Volt::route('/exportar', 'admin.exports.index')
            ->name('admin.exports.index');

        // Usuários
        Route::prefix('/usuarios')->group(function () {
            Volt::route('/', 'admin.users.index')->name('admin.users.index');
            Volt::route('/novo', 'admin.users.create')->name('admin.users.create');
            Volt::route('/editar/{id}', 'admin.users.update')->name('admin.users.update');
        });

        // Perfis e Permissões
        Route::prefix('/perfis')->group(function () {
            Volt::route('/', 'admin.profile.index')->name('admin.profile.index');
            Volt::route('/editar/{id}', 'admin.profile.update')->name('admin.profile.update');
            Volt::route('/editar-permissoes/{id}', 'admin.profile.update-permissions')
                ->name('admin.profile.update-permissions');
        });
    });
});

// ---------------------------------------------------------------------
// ROTAS DE AUTENTICAÇÃO
// ---------------------------------------------------------------------
require __DIR__ . '/auth.php';
