<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quebras_aprovacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quebra_id')
                  ->constrained('quebras')
                  ->cascadeOnDelete();
            $table->foreignId('aprovador_id')
                  ->constrained('users');
            $table->enum('status', ['aprovada', 'reprovada']);
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quebras_aprovacoes');
    }
};
