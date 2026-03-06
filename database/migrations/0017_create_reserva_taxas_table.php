<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_taxas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->restrictOnDelete();
            $table->foreignId('taxa_id')->constrained('taxas')->restrictOnDelete();
            $table->decimal('valor_aplicado', 10, 2);
            $table->timestamps();

            $table->index('reserva_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_taxas');
    }
};