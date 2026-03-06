<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_dias_disponiveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->enum('dia_semana', ['DOMINGO', 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO']);
            $table->timestamps();

            $table->unique(['area_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_dias_disponiveis');
    }
};