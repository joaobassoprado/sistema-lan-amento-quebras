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
// ÁREA LOGADA (SOMENTE AUTH)
// ---------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));

    Volt::route('/dashboard', 'dashboard')->name('dashboard');

    // -----------------------------------------------------------------
    // MÓDULO DE QUEBRAS
    // -----------------------------------------------------------------
    Route::prefix('/quebras')->group(function () {
        
        // TODOS TÊM ACESSO (O controle é feito dentro do componente se necessário)
        Volt::route('/lancar', 'quebras.create')
            ->name('quebras.create');

        Volt::route('/resumo', 'quebras.resumo') // Ajustado para bater com o arquivo resumo.blade
            ->name('quebras.resumo');

        // ADMIN VÊ A TELA, USUÁRIO COMUM VÊ O "ACESSO RESTRITO"
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
    // APIs DE BUSCA (UTILIZADAS NO FORMULÁRIO DE LANÇAMENTO)
    // -----------------------------------------------------------------
    Route::get('/api/produtos', function (Request $request) {
        $q = trim($request->get('q'));
        if (strlen($q) < 3) return [];

        return Produto::on200()->select('código as id', 'nome')
            ->where('nome', 'like', "{$q}%")
            ->orWhere('código', 'like', "{$q}%")
            ->orderBy('nome')
            ->limit(10)
            ->get();
    });

    Route::get('/api/funcionarios', function (Request $request) {
        $q = trim($request->get('q'));
        if (strlen($q) < 3) return [];

        return Funcionario::on200()->select('codigo as id', 'nome')
            ->where('nome', 'like', "%{$q}%")
            ->orderBy('nome')
            ->limit(10)
            ->get();
    });

    // -----------------------------------------------------------------
    // ÁREA ADMINISTRATIVA
    // -----------------------------------------------------------------
    Route::prefix('/admin')->group(function () {

        Volt::route('/exportar', 'admin.exports.index')
            ->name('admin.exports.index');

        Route::prefix('/usuarios')->group(function () {
            Volt::route('/', 'admin.users.index')->name('admin.users.index');
            Volt::route('/novo', 'admin.users.create')->name('admin.users.create');
            Volt::route('/editar/{id}', 'admin.users.update')->name('admin.users.update');
        });

        Route::prefix('/perfis')->group(function () {
            Volt::route('/', 'admin.profile.index')->name('admin.profile.index');
            Volt::route('/editar/{id}', 'admin.profile.update')->name('admin.profile.update');
            Volt::route('/editar-permissoes/{id}', 'admin.profile.update-permissions')
                ->name('admin.profile.update-permissions');
        });
    });
});

require __DIR__ . '/auth.php';