<?php

namespace App\Http\Controllers;

use App\Models\{Area, AreaValor};
use Illuminate\Http\{JsonResponse, Request};

class AreaValorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'area_id' => 'nullable|integer|exists:areas,id',
            'ativo'   => 'nullable|boolean',
        ]);

        $query = AreaValor::with(['area' => fn($q) => $q->withTrashed()]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->integer('area_id'));
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        return response()->json($query->orderBy('area_id')->orderBy('tipo_reserva')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_id'       => 'required|exists:areas,id',
            'tipo_reserva'  => 'required|in:FIXA,UNICA,MENSALISTA',
            'modo_cobranca' => 'required|in:HORA,DIA,MES,VALOR_FECHADO',
            'valor'         => 'required|numeric|min:0.01|max:99999.99',
            'dia_semana'    => 'nullable|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'descricao'     => 'nullable|string|max:500',
        ]);

        return response()->json(AreaValor::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $areaValor = AreaValor::findOrFail($id);

        $validated = $request->validate([
            'area_id'       => 'sometimes|exists:areas,id',
            'tipo_reserva'  => 'sometimes|in:FIXA,UNICA,MENSALISTA',
            'modo_cobranca' => 'sometimes|in:HORA,DIA,MES,VALOR_FECHADO',
            'valor'         => 'sometimes|numeric|min:0.01|max:99999.99',
            'dia_semana'    => 'nullable|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'descricao'     => 'nullable|string|max:500',
        ]);

        $valorMudou = isset($validated['valor'])
            && (float) $validated['valor'] !== (float) $areaValor->valor;

        $modoCobrancaMudou = isset($validated['modo_cobranca'])
            && $validated['modo_cobranca'] !== $areaValor->modo_cobranca;

        if ($areaValor->temVinculo() && ($valorMudou || $modoCobrancaMudou)) {
            $areaValor->update(['ativo' => false]);
            $novoValor = AreaValor::create(array_merge(
                $areaValor->only(['area_id', 'tipo_reserva', 'modo_cobranca', 'valor', 'dia_semana', 'descricao']),
                $validated,
            ));
            return response()->json($novoValor);
        }

        $areaValor->update($validated);

        return response()->json($areaValor);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $areaValor = AreaValor::findOrFail($id);
        $areaValor->update(['ativo' => !$areaValor->ativo]);

        return response()->json([
            'ativo'   => $areaValor->ativo,
            'message' => $areaValor->ativo ? 'Valor ativado' : 'Valor desativado',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $areaValor = AreaValor::findOrFail($id);

        if ($areaValor->temVinculo()) {
            return response()->json([
                'message' => 'Este valor está vinculado a reservas. Desative-o ao invés de excluir.',
            ], 422);
        }

        $areaValor->delete();

        return response()->json(['message' => 'Valor excluído']);
    }

    public function restore(int $id): JsonResponse
    {
        $areaValor = AreaValor::onlyTrashed()->findOrFail($id);
        $areaValor->restore();

        return response()->json(['message' => 'Valor restaurado']);
    }
}
