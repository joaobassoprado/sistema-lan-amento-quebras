<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarFuncionarios200 extends Command
{
    protected $signature = 'importar:funcionarios200';
    protected $description = 'Importa funcionarios do banco 200';

    public function handle()
    {
        $this->info('Importando funcionários...');

        $funcionarios = DB::connection('db200')->select(
            'SELECT codigo, nome, setor FROM bases.funcionarios'
        );

        foreach ($funcionarios as $f) {
            DB::table('funcionarios')->updateOrInsert(
                ['codigo_externo' => $f->codigo],
                [
                    'nome' => $f->nome,
                    'setor' => $f->setor,
                    'ativo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Funcionários importados com sucesso!');
    }
}
