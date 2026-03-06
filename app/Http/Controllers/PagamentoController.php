<?php

namespace App\Http\Controllers;

use App\Models\{Cliente, Pagamento};
use Illuminate\Http\{JsonResponse, Request};

class PagamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pagamento::with([
            'cliente' => fn($q) => $q->withTrashed(),
            'reserva' => fn($q) => $q->withTrashed(),
        ]);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('referencia_mes')) {
            $query->where('referencia_mes', $request->referencia_mes);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [$request->data_inicio, $request->data_fim . ' 23:59:59']);
        }

        if ($request->filled('busca')) {
            $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->busca) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('obs', 'like', $termo)
                    ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', $termo));
            });
        }

        $query->orderByDesc('created_at');

        if ($request->expectsJson()) {
            return response()->json($query->get());
        }

        $pagamentos = $query->paginate(30);

        $stats = [
            'total_pago'     => Pagamento::where('status', 'PAGO')->where('tipo', 'PAGAMENTO')->sum('valor'),
            'total_pendente' => Pagamento::where('status', 'PENDENTE')->sum('valor'),
            'total_creditos' => Pagamento::where('tipo', 'CREDITO')->where('status', 'PAGO')->sum('valor'),
            'total_debitos'  => Pagamento::where('tipo', 'DEBITO')->where('status', 'PENDENTE')->sum('valor'),
        ];

        return view('pagamentos.index', [
            'pagamentos' => $pagamentos,
            'clientes'   => Cliente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
            'stats'      => $stats,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'reserva_id'       => 'nullable|exists:reservas,id',
            'tipo'             => 'required|in:PAGAMENTO,CREDITO,DEBITO',
            'valor'            => 'required|numeric|min:0.01|max:999999.99',
            'data_vencimento'  => 'nullable|date',
            'data_pagamento'   => 'nullable|date',
            'status'           => 'required|in:PENDENTE,PAGO,ATRASADO,CANCELADO',
            'forma_pagamento'  => 'nullable|string|max:191',
            'referencia_mes'   => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'obs'              => 'nullable|string|max:1000',
        ]);

        return response()->json(Pagamento::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $pagamento = Pagamento::findOrFail($id);

        $validated = $request->validate([
            'cliente_id'       => 'sometimes|exists:clientes,id',
            'reserva_id'       => 'nullable|exists:reservas,id',
            'tipo'             => 'sometimes|in:PAGAMENTO,CREDITO,DEBITO',
            'valor'            => 'sometimes|numeric|min:0.01|max:999999.99',
            'data_vencimento'  => 'nullable|date',
            'data_pagamento'   => 'nullable|date',
            'status'           => 'sometimes|in:PENDENTE,PAGO,ATRASADO,CANCELADO',
            'forma_pagamento'  => 'nullable|string|max:191',
            'referencia_mes'   => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'obs'              => 'nullable|string|max:1000',
        ]);

        $pagamento->update($validated);

        return response()->json($pagamento);
    }


    public function exportarXlsx(Request $request)
    {
        $query = Pagamento::with([
            'cliente' => fn($q) => $q->withTrashed(),
            'reserva' => fn($q) => $q->withTrashed()->with(['area' => fn($q2) => $q2->withTrashed()]),
        ]);

        if ($request->filled('cliente_id')) $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('referencia_mes')) $query->where('referencia_mes', $request->referencia_mes);

        $query->orderByDesc('created_at');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PagamentosExport($query->get()),
            'pagamentos_' . now()->format('Y-m-d_His') . '.xlsx',
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $pagamento = Pagamento::findOrFail($id);
        $pagamento->delete();

        return response()->json(['message' => 'Pagamento excluído']);
    }

    public function restore(int $id): JsonResponse
    {
        $pagamento = Pagamento::onlyTrashed()->findOrFail($id);
        $pagamento->restore();

        return response()->json(['message' => 'Pagamento restaurado']);
    }
}
