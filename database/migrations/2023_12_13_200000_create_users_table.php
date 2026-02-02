<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // LOGIN
            $table->string('name')->unique(); // usuário
            $table->string('password');

            // DADOS
            $table->string('nome_completo')->nullable();
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->nullOnDelete();

            // AUDITORIA
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
