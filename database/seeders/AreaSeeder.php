<?php

namespace Database\Seeders;

use App\Models\{Area, AreaDiaDisponivel, TipoArea};
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    private const TODOS_DIAS = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];
    private const SEMANA = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA'];
    private const FDS = ['SABADO', 'DOMINGO'];

    public function run(): void
    {
        $tiposMap = TipoArea::pluck('id', 'nome');
        $quadraId = $tiposMap['QUADRA'];
        $churrasId = $tiposMap['CHURRASQUEIRA'];

        $this->criarQuadra1($quadraId);
        $this->criarQuadra2($quadraId);
        $this->criarQuadraFutsal($quadraId);
        $this->criarChurrasqueiras($churrasId);
    }

    private function criarQuadra1(int $tipoId): void
    {
        $area = Area::firstOrCreate(['nome' => 'Quadra 1'], [
            'tipo_area_id'       => $tipoId,
            'descricao'          => 'Grama sintética',
            'capacidade_pessoas' => 30,
            'modo_reserva'       => 'HORARIO',
            'duracao_slot_min'   => 60,
        ]);

        $this->popularDias($area->id, self::TODOS_DIAS, '07:00', '23:00');
    }

    private function criarQuadra2(int $tipoId): void
    {
        $area = Area::firstOrCreate(['nome' => 'Quadra 2'], [
            'tipo_area_id'       => $tipoId,
            'descricao'          => 'Grama sintética',
            'capacidade_pessoas' => 30,
            'modo_reserva'       => 'HORARIO',
            'duracao_slot_min'   => 60,
        ]);

        $this->popularDias($area->id, self::SEMANA, '07:20', '23:20');
        $this->popularDias($area->id, self::FDS, '07:00', '23:00');
    }

    private function criarQuadraFutsal(int $tipoId): void
    {
        $area = Area::firstOrCreate(['nome' => 'Quadra Futsal'], [
            'tipo_area_id'       => $tipoId,
            'descricao'          => 'Grama natural',
            'capacidade_pessoas' => 20,
            'modo_reserva'       => 'HORARIO',
            'duracao_slot_min'   => 60,
        ]);

        $this->popularDias($area->id, self::SEMANA, '07:20', '23:20');
        $this->popularDias($area->id, self::FDS, '07:00', '23:00');
    }

    private function criarChurrasqueiras(int $churrasId): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $area = Area::firstOrCreate(["nome" => "Churrasqueira {$i}"], [
                'tipo_area_id' => $churrasId,
                'modo_reserva' => 'DIA_INTEIRO',
            ]);

            $this->popularDias($area->id, self::TODOS_DIAS);
        }
    }

    private function popularDias(int $areaId, array $dias, ?string $abertura = null, ?string $fechamento = null): void
    {
        foreach ($dias as $dia) {
            AreaDiaDisponivel::firstOrCreate(
                ['area_id' => $areaId, 'dia_semana' => $dia],
                ['horario_abertura' => $abertura, 'horario_fechamento' => $fechamento],
            );
        }
    }
}
