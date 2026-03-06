<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_area', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('icone')->default('fi-rr-marker');
            $table->string('cor')->default('#3b82f6');
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_area');
    }
};