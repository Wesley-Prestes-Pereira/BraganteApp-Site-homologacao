<?php

namespace App\Http\Controllers;

use App\Models\{Cliente, Pagamento};
use Illuminate\Http\{JsonResponse, Request};

class PagamentoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'cliente_id'     => 'nullable|integer|exists:clientes,id',
            'status'         => 'nullable|in:PENDENTE,PAGO,ATRASADO,CANCELADO',
            'tipo'           => 'nullable|in:PAGAMENTO,CREDITO,DEBITO',
            'referencia_mes' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'data_inicio'    => 'nullable|date',
            'data_fim'       => 'nullable|date|after_or_equal:data_inicio',
            'busca'          => 'nullable|string|max:191',
        ]);

        $query = Pagamento::with([
            'cliente' => fn($q) => $q->withTrashed(),
            'reserva' => fn($q) => $q->withTrashed(),
        ]);

        $this->aplicarFiltros($query, $request);

        $query->orderByDesc('created_at');

        if ($request->expectsJson()) {
            return response()->json($query->get());
        }

        $pagamentos = $query->paginate(30);

        $statsQuery = Pagamento::query();
        $this->aplicarFiltros($statsQuery, $request);

        $stats = [
            'total_pago'     => (clone $statsQuery)->where('status', 'PAGO')->where('tipo', 'PAGAMENTO')->sum('valor'),
            'total_pendente' => (clone $statsQuery)->where('status', 'PENDENTE')->sum('valor'),
            'total_creditos' => (clone $statsQuery)->where('tipo', 'CREDITO')->where('status', 'PAGO')->sum('valor'),
            'total_debitos'  => (clone $statsQuery)->where('tipo', 'DEBITO')->where('status', 'PENDENTE')->sum('valor'),
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

        $cliente = Cliente::find($validated['cliente_id']);
        if (!$cliente || !$cliente->ativo) {
            return response()->json([
                'message' => 'Este cliente está desativado.',
                'errors'  => ['cliente_id' => ['Este cliente está desativado e não pode receber novos registros financeiros.']],
            ], 422);
        }

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

        if (isset($validated['cliente_id']) && (int) $validated['cliente_id'] !== $pagamento->cliente_id) {
            if ($pagamento->reserva_id) {
                return response()->json([
                    'message' => 'Não é possível alterar o cliente de um pagamento vinculado a uma reserva.',
                ], 422);
            }

            $novoCliente = Cliente::find($validated['cliente_id']);
            if (!$novoCliente || !$novoCliente->ativo) {
                return response()->json([
                    'message' => 'O cliente de destino está desativado.',
                    'errors'  => ['cliente_id' => ['O cliente de destino está desativado.']],
                ], 422);
            }
        }

        $pagamento->update($validated);

        return response()->json($pagamento);
    }

    public function exportarXlsx(Request $request)
    {
        $query = Pagamento::with([
            'cliente' => fn($q) => $q->withTrashed(),
            'reserva' => fn($q) => $q->withTrashed()->with(['area' => fn($q2) => $q2->withTrashed()]),
        ]);

        $this->aplicarFiltros($query, $request);

        $query->orderByDesc('created_at');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PagamentosExport($query->get()),
            'pagamentos_' . now()->format('Y-m-d_His') . '.xlsx',
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $pagamento = Pagamento::findOrFail($id);

        if ($pagamento->status === 'PAGO' && $pagamento->reserva_id) {
            $reservaAtiva = $pagamento->reserva()
                ->whereNull('deleted_at')
                ->exists();

            if ($reservaAtiva) {
                return response()->json([
                    'message' => 'Este pagamento está vinculado a uma reserva ativa. Cancele-o ao invés de excluir.',
                ], 422);
            }
        }

        $pagamento->delete();

        return response()->json(['message' => 'Pagamento excluído']);
    }

    public function restore(int $id): JsonResponse
    {
        $pagamento = Pagamento::onlyTrashed()->findOrFail($id);
        $pagamento->restore();

        return response()->json(['message' => 'Pagamento restaurado']);
    }

    private function aplicarFiltros($query, Request $request): void
    {
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->integer('cliente_id'));
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
            $query->whereBetween('created_at', [
                $request->date('data_inicio')->startOfDay(),
                $request->date('data_fim')->endOfDay(),
            ]);
        }

        if ($request->filled('busca')) {
            $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->busca) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('obs', 'like', $termo)
                    ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', $termo));
            });
        }
    }
}
