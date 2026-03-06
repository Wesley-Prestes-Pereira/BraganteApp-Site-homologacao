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
            'TERCA' => 'TER',
            'QUARTA' => 'QUA',
            'QUINTA' => 'QUI',
            'SEXTA' => 'SEX',
            'SABADO' => 'SÁB',
            'DOMINGO' => 'DOM',
        ];
        $todosDias = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];

        $areasComReservas = Area::where('tipo_area_id', $tipoAreaId)
            ->whereHas('reservas', fn($q) => $q->withTrashed())
            ->pluck('id');

        $areasProcessadas = $areas->map(function ($area) use ($todosDias, $areasComReservas) {
            $area->dias_lista = $area->diasCached();
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
            'nome'               => 'required|string|max:191|unique:areas,nome',
            'tipo_area_id'       => 'required|integer|exists:tipos_area,id',
            'descricao'          => 'nullable|string|max:500',
            'capacidade_pessoas' => 'nullable|integer|min:1|max:9999',
            'modo_reserva'       => 'required|in:HORARIO,DIA_INTEIRO',
            'dias'               => 'required|array|min:1',
            'dias.*'             => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'horarios'           => 'nullable|array',
            'horarios.*'         => 'date_format:H:i',
        ]);

        $area = DB::transaction(function () use ($validated) {
            $area = Area::create([
                'nome'               => $validated['nome'],
                'tipo_area_id'       => $validated['tipo_area_id'],
                'descricao'          => $validated['descricao'] ?? null,
                'capacidade_pessoas' => $validated['capacidade_pessoas'] ?? null,
                'modo_reserva'       => $validated['modo_reserva'],
            ]);

            $this->syncDiasInterno($area, $validated['dias']);

            if ($validated['modo_reserva'] === 'HORARIO' && !empty($validated['horarios'])) {
                $this->syncHorariosInterno($area, $validated['dias'], $validated['horarios']);
            }

            return $area;
        });

        return response()->json($area->load('tipoArea'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        $validated = $request->validate([
            'nome'               => "sometimes|string|max:191|unique:areas,nome,{$area->id}",
            'tipo_area_id'       => 'sometimes|integer|exists:tipos_area,id',
            'descricao'          => 'nullable|string|max:500',
            'capacidade_pessoas' => 'nullable|integer|min:1|max:9999',
            'modo_reserva'       => 'sometimes|in:HORARIO,DIA_INTEIRO',
            'dias'               => 'sometimes|array|min:1',
            'dias.*'             => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'horarios'           => 'nullable|array',
            'horarios.*'         => 'date_format:H:i',
        ]);

        DB::transaction(function () use ($area, $validated) {
            $area->update(collect($validated)->except(['dias', 'horarios'])->toArray());

            if (isset($validated['dias'])) {
                $this->syncDiasInterno($area, $validated['dias']);

                $modo = $validated['modo_reserva'] ?? $area->modo_reserva;
                if ($modo === 'HORARIO' && !empty($validated['horarios'])) {
                    $area->horariosConfig()->forceDelete();
                    $this->syncHorariosInterno($area, $validated['dias'], $validated['horarios']);
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

        return response()->json([
            'area_id'       => $area->id,
            'modo_reserva'  => $area->modo_reserva,
            'dias'          => $area->diasCached(),
            'horarios'      => $area->todosHorariosCached(),
        ]);
    }

    public function syncHorarios(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        if ($area->modo_reserva === 'DIA_INTEIRO') {
            return response()->json(['message' => 'Áreas com reserva por dia inteiro não possuem horários.'], 422);
        }

        $validated = $request->validate([
            'horarios'              => 'required|array',
            'horarios.*.dia_semana' => 'required|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'horarios.*.horario'    => 'required|date_format:H:i',
            'horarios.*.ativo'      => 'boolean',
        ]);

        DB::transaction(function () use ($area, $validated) {
            $area->horariosConfig()->delete();

            foreach ($validated['horarios'] as $h) {
                AreaHorario::create([
                    'area_id'    => $area->id,
                    'dia_semana' => $h['dia_semana'],
                    'horario'    => $h['horario'],
                    'ativo'      => $h['ativo'] ?? true,
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
            'dias'   => 'required|array|min:1',
            'dias.*' => 'in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
        ]);

        $this->syncDiasInterno($area, $validated['dias']);
        Area::limparCache();

        return response()->json(['message' => 'Dias atualizados']);
    }

    private function syncDiasInterno(Area $area, array $dias): void
    {
        $area->diasDisponiveis()->delete();

        foreach ($dias as $dia) {
            AreaDiaDisponivel::create([
                'area_id'    => $area->id,
                'dia_semana' => $dia,
            ]);
        }
    }

    private function syncHorariosInterno(Area $area, array $dias, array $horarios): void
    {
        foreach ($dias as $dia) {
            foreach ($horarios as $horario) {
                AreaHorario::create([
                    'area_id'    => $area->id,
                    'dia_semana' => $dia,
                    'horario'    => $horario,
                    'ativo'      => true,
                ]);
            }
        }
    }
}
