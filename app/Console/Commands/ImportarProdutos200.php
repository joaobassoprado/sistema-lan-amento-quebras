<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarProdutos200 extends Command
{
    protected $signature = 'importar:produtos200';
    protected $description = 'Importa produtos do banco 200';

    public function handle()
    {
        $this->info('Importando produtos...');

        $produtos = DB::connection('db200')->select(
            'SELECT `código` AS codigo_externo, nome FROM `bases`.`produtos`'
        );

        foreach ($produtos as $produto) {
            DB::table('produtos')->updateOrInsert(
                ['codigo_externo' => $produto->codigo_externo],
                [
                    'nome' => $produto->nome,
                    'ativo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Importação finalizada');
    }
}
