<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->enum('tipo_reserva', ['FIXA', 'UNICA', 'MENSALISTA']);
            $table->enum('modo_cobranca', ['HORA', 'DIA', 'MES', 'VALOR_FECHADO']);
            $table->decimal('valor', 10, 2);
            $table->enum('dia_semana', ['DOMINGO', 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO'])->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['area_id', 'tipo_reserva', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_valores');
    }
};