<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('cpf')->nullable()->unique();
            $table->boolean('ativo')->default(true)->index();
            $table->text('obs')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nome');
            $table->index('telefone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};