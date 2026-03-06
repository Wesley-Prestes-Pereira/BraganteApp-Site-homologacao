<?php

namespace App\Http\Controllers;

use App\Models\{Area, AreaDiaDisponivel, AreaHorario, TipoArea};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::with('tipoArea')
            ->withCount(['reservas' => fn($q) => $q->withoutTrashed()])
            ->orderBy('tipo_area_id')
            ->orderBy('nome')
            ->get();

        $tiposArea = TipoArea::allCached();
        $grupos = $areas->groupBy('tipo_area_id');

        $diasAbrev = [
            'SEGUNDA' => 'SEG', 'TERCA' => 'TER', 'QUARTA' => 'QUA',
            'QUINTA' => 'QUI', 'SEXTA' => 'SEX', 'SABADO' => 'SÁB', 'DOMINGO' => 'DOM',
        ];
        $todosDias = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];

        $areasProcessadas = $areas->map(function ($area) use ($todosDias, $diasAbrev) {
            $area->dias_lista = $area->diasCached();
            $area->dias_abrev = $diasAbrev;
            $area->todos_dias = $todosDias;
            return $area;
        });

        $gruposProcessados = $tiposArea->map(fn($tipo) => (object) [
            'tipo'  => $tipo,
            'areas' => $areasProcessadas->filter(fn($a) => $a->tipo_area_id === $tipo->id)->values(),
        ])->filter(fn($g) => $g->areas->isNotEmpty())->values();

        return view('areas.index', [
            'areas'       => $areasProcessadas,
            'tiposArea'   => $tiposArea,
            'grupos'      => $gruposProcessados,
            'diasAbrev'   => $diasAbrev,
            'todosDias'   => $todosDias,
            'totalAreas'  => $areas->count(),
            'inativas'    => $areas->where('ativo', false)->count(),
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
        ]);

        $area->update($validated);

        return response()->json($area->load('tipoArea'));
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
                'message' => 'Esta área possui reservas vinculadas. Desative-a ao invés de excluir.',
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
            'horarios.*.ativo'      => 'required|boolean',
        ]);

        DB::transaction(function () use ($area, $validated) {
            foreach ($validated['horarios'] as $item) {
                AreaHorario::withTrashed()->updateOrCreate(
                    [
                        'area_id'    => $area->id,
                        'dia_semana' => $item['dia_semana'],
                        'horario'    => $item['horario'],
                    ],
                    [
                        'ativo'      => $item['ativo'],
                        'deleted_at' => null,
                    ],
                );
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

        DB::transaction(function () use ($area, $validated) {
            $this->syncDiasInterno($area, $validated['dias']);
        });

        Area::limparCache();

        return response()->json(['message' => 'Dias atualizados']);
    }

    private function syncDiasInterno(Area $area, array $dias): void
    {
        $area->diasDisponiveis()->whereNotIn('dia_semana', $dias)->delete();

        foreach ($dias as $dia) {
            AreaDiaDisponivel::firstOrCreate([
                'area_id'    => $area->id,
                'dia_semana' => $dia,
            ]);
        }
    }

    private function syncHorariosInterno(Area $area, array $dias, array $horarios): void
    {
        foreach ($dias as $dia) {
            foreach ($horarios as $horario) {
                AreaHorario::firstOrCreate([
                    'area_id'    => $area->id,
                    'dia_semana' => $dia,
                    'horario'    => $horario,
                ]);
            }
        }
    }
}