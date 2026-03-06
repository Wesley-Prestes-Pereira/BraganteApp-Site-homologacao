<?php

namespace App\Http\Controllers;

use App\Models\{Area, AreaDiaDisponivel, AreaHorario, TipoArea};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index()
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $query = TipoArea::query();
        if (!$isAdmin) {
            $query->where('ativo', true);
        }

        $tipos = $query
            ->withCount(['areas as areas_ativas_count' => fn($q) => $q->where('ativo', true)])
            ->withCount(['areas as areas_total_count'])
            ->orderBy('nome')
            ->get();

        $reservasPorTipo = DB::table('reservas')
            ->join('areas', 'areas.id', '=', 'reservas.area_id')
            ->whereNull('reservas.deleted_at')
            ->whereNull('areas.deleted_at')
            ->selectRaw('areas.tipo_area_id, COUNT(*) as total')
            ->groupBy('areas.tipo_area_id')
            ->pluck('total', 'tipo_area_id');

        $tiposComReservas = TipoArea::whereHas(
            'areas',
            fn($q) =>
            $q->withTrashed()->whereHas('reservas', fn($r) => $r->withTrashed())
        )->pluck('id');

        $tipos->each(function ($tipo) use ($reservasPorTipo, $tiposComReservas) {
            $tipo->total_reservas = $reservasPorTipo[$tipo->id] ?? 0;
            $tipo->pode_excluir = !$tiposComReservas->contains($tipo->id);
        });

        return view('areas.index', [
            'tipos'   => $tipos,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function porTipo(int $tipoAreaId)
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $tipo = TipoArea::query()
            ->when(!$isAdmin, fn($q) => $q->where('ativo', true))
            ->findOrFail($tipoAreaId);

        $areas = Area::where('tipo_area_id', $tipoAreaId)
            ->when(!$isAdmin, fn($q) => $q->where('ativo', true))
            ->withCount(['reservas' => fn($q) => $q->withoutTrashed()])
            ->orderBy('nome')
            ->get();

        $diasAbrev = [
            'SEGUNDA' => 'SEG',
            'TERCA'   => 'TER',
            'QUARTA'  => 'QUA',
            'QUINTA'  => 'QUI',
            'SEXTA'   => 'SEX',
            'SABADO'  => 'SÁB',
            'DOMINGO' => 'DOM',
        ];
        $todosDias = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];

        $areasComReservas = Area::where('tipo_area_id', $tipoAreaId)
            ->whereHas('reservas', fn($q) => $q->withTrashed())
            ->pluck('id');

        $areasProcessadas = $areas->map(function ($area) use ($todosDias, $areasComReservas) {
            $area->dias_lista = $area->diasCached();
            $area->config_dias = $area->configDiasCached();
            $area->pode_excluir = !$areasComReservas->contains($area->id);
            return $area;
        });

        return view('areas.tipo', [
            'tipo'      => $tipo,
            'areas'     => $areasProcessadas,
            'diasAbrev' => $diasAbrev,
            'todosDias' => $todosDias,
            'isAdmin'   => $isAdmin,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'                            => 'required|string|max:191|unique:areas,nome',
            'tipo_area_id'                    => 'required|integer|exists:tipos_area,id',
            'descricao'                       => 'nullable|string|max:500',
            'capacidade_pessoas'              => 'nullable|integer|min:1|max:9999',
            'modo_reserva'                    => 'required|in:HORARIO,DIA_INTEIRO',
            'duracao_slot_min'                => 'nullable|integer|in:15,30,45,60,90,120',
            'dias'                            => 'required|array|min:1',
            'dias.*'                          => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'config_dias'                     => 'nullable|array',
            'config_dias.*.abertura'          => 'required_with:config_dias|date_format:H:i',
            'config_dias.*.fechamento'        => 'required_with:config_dias|date_format:H:i|after:config_dias.*.abertura',
        ]);

        $area = DB::transaction(function () use ($validated) {
            $area = Area::create([
                'nome'               => $validated['nome'],
                'tipo_area_id'       => $validated['tipo_area_id'],
                'descricao'          => $validated['descricao'] ?? null,
                'capacidade_pessoas' => $validated['capacidade_pessoas'] ?? null,
                'modo_reserva'       => $validated['modo_reserva'],
                'duracao_slot_min'   => $validated['duracao_slot_min'] ?? 60,
            ]);

            $this->syncDiasInterno($area, $validated['dias'], $validated['config_dias'] ?? []);

            return $area;
        });

        return response()->json($area->load('tipoArea'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        $validated = $request->validate([
            'nome'                            => "sometimes|string|max:191|unique:areas,nome,{$area->id}",
            'tipo_area_id'                    => 'sometimes|integer|exists:tipos_area,id',
            'descricao'                       => 'nullable|string|max:500',
            'capacidade_pessoas'              => 'nullable|integer|min:1|max:9999',
            'modo_reserva'                    => 'sometimes|in:HORARIO,DIA_INTEIRO',
            'duracao_slot_min'                => 'nullable|integer|in:15,30,45,60,90,120',
            'dias'                            => 'sometimes|array|min:1',
            'dias.*'                          => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'config_dias'                     => 'nullable|array',
            'config_dias.*.abertura'          => 'required_with:config_dias|date_format:H:i',
            'config_dias.*.fechamento'        => 'required_with:config_dias|date_format:H:i|after:config_dias.*.abertura',
        ]);

        DB::transaction(function () use ($area, $validated) {
            $area->update(collect($validated)->except(['dias', 'config_dias'])->toArray());

            if (isset($validated['dias'])) {
                $this->syncDiasInterno($area, $validated['dias'], $validated['config_dias'] ?? []);

                if ($area->modo_reserva === 'DIA_INTEIRO') {
                    $area->horariosConfig()->withTrashed()->forceDelete();
                }
            }
        });

        return response()->json($area->fresh()->load('tipoArea'));
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->update(['ativo' => !$area->ativo]);

        return response()->json([
            'ativo'   => $area->ativo,
            'message' => $area->ativo ? 'Área ativada' : 'Área desativada',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        if ($area->temVinculo()) {
            return response()->json([
                'message' => 'Esta área possui reservas vinculadas. Para excluir, remova primeiro todas as reservas desta área.',
            ], 422);
        }

        DB::transaction(function () use ($area) {
            $area->horariosConfig()->delete();
            $area->diasDisponiveis()->delete();
            $area->areaTaxas()->delete();
            $area->valores()->delete();
            $area->delete();
        });

        return response()->json(['message' => 'Área excluída']);
    }

    public function restore(int $id): JsonResponse
    {
        $area = Area::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($area) {
            $area->restore();
            $area->horariosConfig()->onlyTrashed()->restore();
            $area->valores()->onlyTrashed()->restore();
        });

        return response()->json(['message' => 'Área restaurada']);
    }

    public function horarios(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        $bloqueados = [];
        $registros = $area->horariosConfig()->where('ativo', false)->get();
        foreach ($registros as $reg) {
            $bloqueados[$reg->dia_semana][] = $reg->horarioFormatado();
        }

        return response()->json([
            'area_id'          => $area->id,
            'modo_reserva'     => $area->modo_reserva,
            'duracao_slot_min' => $area->duracao_slot_min,
            'dias'             => $area->diasCached(),
            'config_dias'      => $area->configDiasCached(),
            'horarios'         => $area->todosHorariosCached(),
            'bloqueados'       => $bloqueados,
        ]);
    }

    public function syncHorarios(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        if ($area->modo_reserva === 'DIA_INTEIRO') {
            return response()->json(['message' => 'Áreas com reserva por dia inteiro não possuem horários.'], 422);
        }

        $validated = $request->validate([
            'bloqueados'              => 'present|array',
            'bloqueados.*.dia_semana' => 'required|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'bloqueados.*.horario'    => 'required|date_format:H:i',
        ]);

        DB::transaction(function () use ($area, $validated) {
            $area->horariosConfig()->withTrashed()->forceDelete();

            foreach ($validated['bloqueados'] as $b) {
                AreaHorario::create([
                    'area_id'    => $area->id,
                    'dia_semana' => $b['dia_semana'],
                    'horario'    => $b['horario'],
                    'ativo'      => false,
                ]);
            }
        });

        Area::limparCache();

        return response()->json(['message' => 'Horários atualizados']);
    }

    public function syncDias(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        $validated = $request->validate([
            'dias'                            => 'required|array|min:1',
            'dias.*'                          => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'config_dias'                     => 'nullable|array',
            'config_dias.*.abertura'          => 'required_with:config_dias|date_format:H:i',
            'config_dias.*.fechamento'        => 'required_with:config_dias|date_format:H:i|after:config_dias.*.abertura',
        ]);

        $this->syncDiasInterno($area, $validated['dias'], $validated['config_dias'] ?? []);
        Area::limparCache();

        return response()->json(['message' => 'Dias atualizados']);
    }

    private function syncDiasInterno(Area $area, array $dias, array $configDias = []): void
    {
        $area->diasDisponiveis()->delete();

        foreach ($dias as $dia) {
            $config = $configDias[$dia] ?? [];

            AreaDiaDisponivel::create([
                'area_id'             => $area->id,
                'dia_semana'          => $dia,
                'horario_abertura'    => $config['abertura'] ?? null,
                'horario_fechamento'  => $config['fechamento'] ?? null,
            ]);
        }
    }
}
