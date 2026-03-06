<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->unsignedSmallInteger('duracao_slot_min')->default(60)->after('modo_reserva');
        });

        Schema::table('area_dias_disponiveis', function (Blueprint $table) {
            $table->time('horario_abertura')->nullable()->after('dia_semana');
            $table->time('horario_fechamento')->nullable()->after('horario_abertura');
        });

        $this->migrarDadosExistentes();
    }

    private function migrarDadosExistentes(): void
    {
        $areas = DB::table('areas')->where('modo_reserva', 'HORARIO')->get();

        foreach ($areas as $area) {
            $horarios = DB::table('area_horarios')
                ->where('area_id', $area->id)
                ->where('ativo', true)
                ->whereNull('deleted_at')
                ->orderBy('horario')
                ->pluck('horario')
                ->map(fn($h) => substr($h, 0, 5))
                ->unique()
                ->values();

            $duracao = 60;
            if ($horarios->count() >= 2) {
                $primeiro = Carbon::createFromFormat('H:i', $horarios[0]);
                $segundo = Carbon::createFromFormat('H:i', $horarios[1]);
                $duracao = $primeiro->diffInMinutes($segundo);
            }

            DB::table('areas')->where('id', $area->id)->update(['duracao_slot_min' => $duracao]);

            $dias = DB::table('area_dias_disponiveis')->where('area_id', $area->id)->get();

            foreach ($dias as $dia) {
                $diaHorarios = DB::table('area_horarios')
                    ->where('area_id', $area->id)
                    ->where('dia_semana', $dia->dia_semana)
                    ->where('ativo', true)
                    ->whereNull('deleted_at')
                    ->orderBy('horario')
                    ->pluck('horario');

                if ($diaHorarios->isNotEmpty()) {
                    DB::table('area_dias_disponiveis')
                        ->where('id', $dia->id)
                        ->update([
                            'horario_abertura'   => $diaHorarios->first(),
                            'horario_fechamento' => $diaHorarios->last(),
                        ]);
                }
            }
        }

        DB::table('area_horarios')->delete();
    }

    public function down(): void
    {
        Schema::table('area_dias_disponiveis', function (Blueprint $table) {
            $table->dropColumn(['horario_abertura', 'horario_fechamento']);
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('duracao_slot_min');
        });
    }
};
