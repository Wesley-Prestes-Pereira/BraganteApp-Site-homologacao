<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('reserva_id')->nullable()->constrained('reservas')->restrictOnDelete();
            $table->enum('tipo', ['PAGAMENTO', 'CREDITO', 'DEBITO']);
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['PENDENTE', 'PAGO', 'ATRASADO', 'CANCELADO'])->default('PENDENTE');
            $table->string('forma_pagamento')->nullable();
            $table->string('referencia_mes', 7)->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cliente_id', 'status']);
            $table->index('referencia_mes');
            $table->index('data_vencimento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};