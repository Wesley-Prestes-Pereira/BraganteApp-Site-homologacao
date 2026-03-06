<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->enum('dia_semana', ['DOMINGO', 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO']);
            $table->enum('tipo', ['FIXA', 'UNICA', 'MENSALISTA'])->default('UNICA');
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->unsignedInteger('slots_ocupados')->default(1);
            $table->unsignedInteger('duracao_real_min')->nullable();
            $table->date('data_reserva')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->decimal('valor_unitario', 10, 2)->nullable();
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->decimal('valor_taxas', 10, 2)->nullable();
            $table->decimal('desconto', 10, 2)->nullable();
            $table->decimal('valor_final', 10, 2)->nullable();
            $table->unsignedInteger('num_pessoas')->nullable();
            $table->time('hora_entrada')->nullable();
            $table->time('hora_saida')->nullable();
            $table->text('obs')->nullable();
            $table->text('obs_sistema')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['area_id', 'dia_semana', 'horario_inicio'], 'reservas_area_dia_horario_idx');
            $table->index('data_reserva');
            $table->index('cliente_id');
            $table->index(['tipo', 'data_reserva']);
            $table->index(['tipo', 'data_inicio', 'data_fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};