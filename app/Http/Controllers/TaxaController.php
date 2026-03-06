<?php

namespace App\Http\Controllers;

use App\Models\Taxa;
use Illuminate\Http\{JsonResponse, Request};

class TaxaController extends Controller
{
    public function index(Request $request)
    {
        $query = Taxa::query()->orderBy('nome');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->expectsJson()) {
            return response()->json($query->get());
        }

        $taxas = $query->get();

        return view('taxas.index', [
            'taxas'   => $taxas,
            'total'   => $taxas->count(),
            'ativas'  => $taxas->where('ativo', true)->count(),
            'inativas' => $taxas->where('ativo', false)->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'          => 'required|string|max:191',
            'valor'         => 'required|numeric|min:0.01|max:99999.99',
            'tipo_cobranca' => 'required|in:FIXO,PERCENTUAL',
        ]);

        return response()->json(Taxa::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $taxa = Taxa::findOrFail($id);

        $validated = $request->validate([
            'nome'          => 'sometimes|string|max:191',
            'valor'         => 'sometimes|numeric|min:0.01|max:99999.99',
            'tipo_cobranca' => 'sometimes|in:FIXO,PERCENTUAL',
        ]);

        $valorMudou = isset($validated['valor'])
            && (float) $validated['valor'] !== (float) $taxa->valor;

        $tipoCobrancaMudou = isset($validated['tipo_cobranca'])
            && $validated['tipo_cobranca'] !== $taxa->tipo_cobranca;

        if ($taxa->temVinculo() && ($valorMudou || $tipoCobrancaMudou)) {
            $taxa->update(['ativo' => false]);
            $novaTaxa = Taxa::create(array_merge(
                $taxa->only(['nome', 'valor', 'tipo_cobranca']),
                $validated,
            ));
            return response()->json($novaTaxa);
        }

        $taxa->update($validated);

        return response()->json($taxa);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $taxa = Taxa::findOrFail($id);
        $taxa->update(['ativo' => !$taxa->ativo]);

        return response()->json([
            'ativo'   => $taxa->ativo,
            'message' => $taxa->ativo ? 'Taxa ativada' : 'Taxa desativada',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $taxa = Taxa::findOrFail($id);

        if ($taxa->temVinculo()) {
            return response()->json([
                'message' => 'Esta taxa está vinculada a reservas. Desative-a ao invés de excluir.',
            ], 422);
        }

        $taxa->delete();

        return response()->json(['message' => 'Taxa excluída']);
    }

    public function restore(int $id): JsonResponse
    {
        $taxa = Taxa::onlyTrashed()->findOrFail($id);
        $taxa->restore();

        return response()->json(['message' => 'Taxa restaurada']);
    }
}
