<?php

namespace App\Http\Controllers;

use App\Models\TipoArea;
use Illuminate\Http\{JsonResponse, Request};

class TipoAreaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TipoArea::allCached());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'  => 'required|string|max:100|unique:tipos_area,nome',
            'icone' => 'required|string|max:100',
            'cor'   => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        return response()->json(TipoArea::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tipo = TipoArea::findOrFail($id);

        $validated = $request->validate([
            'nome'  => "sometimes|string|max:100|unique:tipos_area,nome,{$tipo->id}",
            'icone' => 'sometimes|string|max:100',
            'cor'   => 'sometimes|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $tipo->update($validated);

        return response()->json($tipo);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $tipo = TipoArea::findOrFail($id);
        $tipo->update(['ativo' => !$tipo->ativo]);

        return response()->json([
            'ativo'   => $tipo->ativo,
            'message' => $tipo->ativo ? 'Tipo ativado' : 'Tipo desativado',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $tipo = TipoArea::findOrFail($id);

        if ($tipo->temVinculo()) {
            return response()->json([
                'message' => 'Este tipo possui áreas vinculadas. Desative-o ao invés de excluir.',
            ], 422);
        }

        $tipo->delete();

        return response()->json(['message' => 'Tipo de área excluído']);
    }

    public function restore(int $id): JsonResponse
    {
        $tipo = TipoArea::onlyTrashed()->findOrFail($id);
        $tipo->restore();

        return response()->json(['message' => 'Tipo de área restaurado']);
    }
}