<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quebras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                  ->constrained('produtos');
            $table->foreignId('funcionario_id')
                  ->constrained('funcionarios');
            $table->integer('quantidade');
            $table->text('motivo')->nullable();
            $table->enum('status', ['pendente', 'aprovada', 'reprovada'])
                  ->default('pendente');
            $table->foreignId('created_by')
                  ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quebras');
    }
};
