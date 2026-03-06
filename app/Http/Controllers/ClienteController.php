<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Exports\ClientesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\{JsonResponse, Request};

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query()->orderBy('nome');

        if ($request->filled('busca')) {
            $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->busca) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'like', $termo)
                    ->orWhere('telefone', 'like', $termo)
                    ->orWhere('email', 'like', $termo)
                    ->orWhere('cpf', 'like', $termo);
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->expectsJson()) {
            return response()->json($query->get());
        }

        $clientes = $query->withCount(['reservas' => fn($q) => $q->withoutTrashed()])->get();

        return view('clientes.index', [
            'clientes'       => $clientes,
            'totalClientes'  => $clientes->count(),
            'ativos'         => $clientes->where('ativo', true)->count(),
            'inativos'       => $clientes->where('ativo', false)->count(),
        ]);
    }

    public function buscar(Request $request): JsonResponse
    {
        $request->validate(['termo' => 'required|string|min:2|max:191']);

        $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->termo) . '%';

        $clientes = Cliente::where('ativo', true)
            ->where(function ($q) use ($termo) {
                $q->where('nome', 'like', $termo)
                    ->orWhere('telefone', 'like', $termo)
                    ->orWhere('email', 'like', $termo);
            })
            ->orderBy('nome')
            ->limit(15)
            ->get(['id', 'nome', 'telefone', 'email']);

        return response()->json($clientes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'     => 'required|string|max:191',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:191',
            'cpf'      => ['nullable', 'string', 'max:14', 'unique:clientes,cpf', 'regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/'],
            'obs'      => 'nullable|string|max:1000',
        ]);

        if (isset($validated['cpf'])) {
            $validated['cpf'] = preg_replace('/[^\d]/', '', $validated['cpf']);
            $validated['cpf'] = substr($validated['cpf'], 0, 3) . '.'
                . substr($validated['cpf'], 3, 3) . '.'
                . substr($validated['cpf'], 6, 3) . '-'
                . substr($validated['cpf'], 9, 2);
        }

        return response()->json(Cliente::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nome'     => 'sometimes|string|max:191',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:191',
            'cpf'      => ['nullable', 'string', 'max:14', "unique:clientes,cpf,{$cliente->id}", 'regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/'],
            'obs'      => 'nullable|string|max:1000',
        ]);

        if (isset($validated['cpf'])) {
            $validated['cpf'] = preg_replace('/[^\d]/', '', $validated['cpf']);
            $validated['cpf'] = substr($validated['cpf'], 0, 3) . '.'
                . substr($validated['cpf'], 3, 3) . '.'
                . substr($validated['cpf'], 6, 3) . '-'
                . substr($validated['cpf'], 9, 2);
        }

        $cliente->update($validated);

        return response()->json($cliente);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update(['ativo' => !$cliente->ativo]);

        return response()->json([
            'ativo'   => $cliente->ativo,
            'message' => $cliente->ativo ? 'Cliente ativado' : 'Cliente desativado',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->temVinculo()) {
            return response()->json([
                'message' => 'Este cliente possui reservas ou pagamentos vinculados. Desative-o ao invés de excluir.',
            ], 422);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente excluído']);
    }

    public function exportarXlsx(Request $request)
    {
        $clientes = Cliente::withCount(['reservas' => fn($q) => $q->withoutTrashed()])
            ->orderBy('nome')
            ->get();

        return Excel::download(
            new ClientesExport($clientes),
            'clientes_' . now()->format('Y-m-d_His') . '.xlsx',
        );
    }

    public function restore(int $id): JsonResponse
    {
        $cliente = Cliente::onlyTrashed()->findOrFail($id);
        $cliente->restore();

        return response()->json(['message' => 'Cliente restaurado']);
    }
}
