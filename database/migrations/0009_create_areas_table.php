<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->foreignId('tipo_area_id')->constrained('tipos_area')->restrictOnDelete();
            $table->text('descricao')->nullable();
            $table->unsignedInteger('capacidade_pessoas')->nullable();
            $table->enum('modo_reserva', ['HORARIO', 'DIA_INTEIRO'])->default('HORARIO');
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};