@extends('layouts.app')

@section('title', 'Financeiro')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Financeiro</h1>
            <p class="page-header__sub">Controle de pagamentos, créditos e débitos</p>
        </div>
        <div style="display:flex;gap:6px;">
            @can('financeiro.ver')
                <a href="{{ route('pagamentos.exportar.xlsx', request()->query()) }}" class="act-btn-label">
                    <i class="fi fi-rr-download"></i> Excel
                </a>
            @endcan
            @can('financeiro.criar')
                <button class="act-btn" onclick="abrirModal()"><i class="fi fi-rr-plus"></i> Novo Registro</button>
            @endcan
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .act-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 40px;
            padding: 0 18px;
            border-radius: 10px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-size: .84rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s
        }

        .act-btn:hover {
            background: var(--accent-h)
        }

        .act-btn-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 40px;
            padding: 0 16px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: var(--card);
            color: var(--t3);
            font-size: .82rem;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s
        }

        .act-btn-label:hover {
            color: var(--t1);
            border-color: var(--input-border-h)
        }

        .p-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px
        }

        .p-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px
        }

        .p-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem
        }

        .p-stat__val {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--t1)
        }

        .p-stat__lbl {
            font-size: .78rem;
            color: var(--t3);
            margin-top: 2px
        }

        .p-filters {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end
        }

        .p-filters .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 140px
        }

        .p-filters .field label {
            font-size: .72rem;
            font-weight: 600;
            color: var(--t4);
            text-transform: uppercase;
            letter-spacing: .3px
        }

        .p-filters .sdb-input,
        .p-filters .sdb-select {
            height: 36px;
            font-size: .82rem;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--t1);
            font-family: inherit;
            outline: none
        }

        .p-filters .sdb-input:focus,
        .p-filters .sdb-select:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow)
        }

        .p-filters__actions {
            display: flex;
            gap: 6px;
            align-items: center
        }

        .p-btn-filter {
            height: 36px;
            padding: 0 14px;
            border-radius: 8px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer
        }

        .p-btn-clear {
            height: 36px;
            width: 36px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .88rem
        }

        .p-table-wrap {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: hidden
        }

        .p-table {
            width: 100%;
            border-collapse: collapse
        }

        .p-table th {
            padding: 12px 14px;
            font-size: .74rem;
            font-weight: 700;
            color: var(--t4);
            text-transform: uppercase;
            letter-spacing: .3px;
            text-align: left;
            border-bottom: 1px solid var(--card-border);
            background: var(--input-bg)
        }

        .p-table td {
            padding: 12px 14px;
            font-size: .84rem;
            color: var(--t2);
            border-bottom: 1px solid var(--card-border);
            vertical-align: middle
        }

        .p-table tr:last-child td {
            border-bottom: none
        }

        .p-table tr:hover td {
            background: var(--hover)
        }

        .p-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: .68rem;
            font-weight: 700
        }

        .p-badge--pagamento {
            background: rgba(52, 211, 153, .12);
            color: var(--success)
        }

        .p-badge--credito {
            background: rgba(91, 156, 246, .12);
            color: var(--accent)
        }

        .p-badge--debito {
            background: rgba(248, 113, 113, .12);
            color: var(--danger)
        }

        .p-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: .68rem;
            font-weight: 700
        }

        .p-status--pago {
            background: rgba(52, 211, 153, .12);
            color: var(--success)
        }

        .p-status--pendente {
            background: rgba(251, 191, 36, .12);
            color: var(--warning)
        }

        .p-status--atrasado {
            background: rgba(248, 113, 113, .12);
            color: var(--danger)
        }

        .p-status--cancelado {
            background: rgba(110, 128, 154, .12);
            color: var(--t3)
        }

        .p-valor {
            font-weight: 700;
            white-space: nowrap
        }

        .p-valor--pos {
            color: var(--success)
        }

        .p-valor--neg {
            color: var(--danger)
        }

        .p-cliente {
            font-weight: 600;
            color: var(--t1)
        }

        .p-actions {
            display: flex;
            gap: 4px
        }

        .btn-ic {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            cursor: pointer;
            font-size: .78rem;
            transition: all .15s
        }

        .btn-ic:hover {
            color: var(--t1);
            background: var(--hover)
        }

        .btn-ic.danger:hover {
            color: var(--danger);
            border-color: var(--danger)
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--t4)
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px
        }

        .modal-overlay.is-open {
            display: flex
        }

        .modal-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            width: 100%;
            max-width: 540px;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto
        }

        .modal-head {
            padding: 20px 24px 0
        }

        .modal-head__title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--t1)
        }

        .modal-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t3);
            margin-bottom: 6px
        }

        .req {
            color: var(--danger)
        }

        .sdb-input,
        .sdb-select,
        .sdb-textarea {
            width: 100%;
            height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--t1);
            font-size: .88rem;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s
        }

        .sdb-textarea {
            height: 80px;
            padding: 10px 14px;
            resize: vertical
        }

        .sdb-input:focus,
        .sdb-select:focus,
        .sdb-textarea:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow)
        }

        .modal-foot {
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid var(--card-border)
        }

        .btn-cancel {
            height: 40px;
            padding: 0 18px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            font-size: .84rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer
        }

        .btn-save {
            height: 40px;
            padding: 0 20px;
            border-radius: 10px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-size: .84rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center
        }

        .confirm-overlay.is-open {
            display: flex
        }

        .confirm-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            max-width: 380px;
            width: 100%
        }

        .confirm-box__title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 8px
        }

        .confirm-box__msg {
            font-size: .84rem;
            color: var(--t3);
            margin-bottom: 20px
        }

        .confirm-box__actions {
            display: flex;
            gap: 8px;
            justify-content: center
        }

        .btn-danger {
            height: 40px;
            padding: 0 20px;
            border-radius: 10px;
            border: none;
            background: var(--danger);
            color: #fff;
            font-size: .84rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer
        }

        .p-pag {
            padding: 16px 20px;
            display: flex;
            justify-content: center
        }

        .p-pag nav {
            display: flex;
            gap: 4px
        }

        @media(max-width:768px) {
            .p-stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .p-filters {
                flex-direction: column
            }

            .p-table-wrap {
                overflow-x: auto
            }

            .field-row {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:480px) {
            .p-stats {
                grid-template-columns: 1fr
            }
        }
    </style>
@endsection

@section('content')
    <div class="p-stats">
        <div class="p-stat">
            <div class="p-stat__ic" style="background:rgba(52,211,153,.15);color:var(--success)"><i
                    class="fi fi-rr-check-circle"></i></div>
            <div>
                <div class="p-stat__val">R$ {{ number_format($stats['total_pago'], 2, ',', '.') }}</div>
                <div class="p-stat__lbl">Total Pago</div>
            </div>
        </div>
        <div class="p-stat">
            <div class="p-stat__ic" style="background:rgba(251,191,36,.15);color:var(--warning)"><i
                    class="fi fi-rr-time-half-past"></i></div>
            <div>
                <div class="p-stat__val">R$ {{ number_format($stats['total_pendente'], 2, ',', '.') }}</div>
                <div class="p-stat__lbl">Pendente</div>
            </div>
        </div>
        <div class="p-stat">
            <div class="p-stat__ic" style="background:rgba(91,156,246,.15);color:var(--accent)"><i
                    class="fi fi-rr-arrow-down"></i></div>
            <div>
                <div class="p-stat__val">R$ {{ number_format($stats['total_creditos'], 2, ',', '.') }}</div>
                <div class="p-stat__lbl">Créditos</div>
            </div>
        </div>
        <div class="p-stat">
            <div class="p-stat__ic" style="background:rgba(248,113,113,.15);color:var(--danger)"><i
                    class="fi fi-rr-arrow-up"></i></div>
            <div>
                <div class="p-stat__val">R$ {{ number_format($stats['total_debitos'], 2, ',', '.') }}</div>
                <div class="p-stat__lbl">Débitos</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('pagamentos.index') }}" class="p-filters">
        <div class="field">
            <label>Cliente</label>
            <select name="cliente_id" class="sdb-select">
                <option value="">Todos</option>
                @foreach ($clientes as $c)
                    <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Tipo</label>
            <select name="tipo" class="sdb-select">
                <option value="">Todos</option>
                <option value="PAGAMENTO" {{ request('tipo') === 'PAGAMENTO' ? 'selected' : '' }}>Pagamento</option>
                <option value="CREDITO" {{ request('tipo') === 'CREDITO' ? 'selected' : '' }}>Crédito</option>
                <option value="DEBITO" {{ request('tipo') === 'DEBITO' ? 'selected' : '' }}>Débito</option>
            </select>
        </div>
        <div class="field">
            <label>Status</label>
            <select name="status" class="sdb-select">
                <option value="">Todos</option>
                <option value="PAGO" {{ request('status') === 'PAGO' ? 'selected' : '' }}>Pago</option>
                <option value="PENDENTE" {{ request('status') === 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                <option value="ATRASADO" {{ request('status') === 'ATRASADO' ? 'selected' : '' }}>Atrasado</option>
                <option value="CANCELADO" {{ request('status') === 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div class="field">
            <label>Mês Ref.</label>
            <input type="month" name="referencia_mes" class="sdb-input" value="{{ request('referencia_mes') }}">
        </div>
        <div class="field">
            <label>Busca</label>
            <input type="text" name="busca" class="sdb-input" value="{{ request('busca') }}" placeholder="Buscar...">
        </div>
        <div class="p-filters__actions">
            <button type="submit" class="p-btn-filter"><i class="fi fi-rr-search"></i></button>
            <a href="{{ route('pagamentos.index') }}" class="p-btn-clear" title="Limpar"><i
                    class="fi fi-rr-cross-small"></i></a>
        </div>
    </form>

    @if ($pagamentos->isEmpty())
        <div class="empty-state">
            <i class="fi fi-rr-coins"></i>
            <p>Nenhum registro financeiro encontrado</p>
        </div>
    @else
        <div class="p-table-wrap">
            <table class="p-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Forma</th>
                        <th>Mês Ref.</th>
                        <th>Vencimento</th>
                        <th>Pagamento</th>
                        <th>Obs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagamentos as $p)
                        <tr>
                            <td><span class="p-cliente">{{ $p->cliente->nome ?? '—' }}</span></td>
                            <td><span class="p-badge p-badge--{{ strtolower($p->tipo) }}">{{ $p->tipo }}</span></td>
                            <td>
                                <span class="p-valor {{ $p->tipo === 'DEBITO' ? 'p-valor--neg' : 'p-valor--pos' }}">
                                    R$ {{ number_format((float) $p->valor, 2, ',', '.') }}
                                </span>
                            </td>
                            <td><span class="p-status p-status--{{ strtolower($p->status) }}">{{ $p->status }}</span>
                            </td>
                            <td>{{ $p->forma_pagamento ?? '—' }}</td>
                            <td>{{ $p->referencia_mes ?? '—' }}</td>
                            <td>{{ $p->data_vencimento ? $p->data_vencimento->format('d/m/Y') : '—' }}</td>
                            <td>{{ $p->data_pagamento ? $p->data_pagamento->format('d/m/Y') : '—' }}</td>
                            <td title="{{ $p->obs }}">{{ $p->obs ? Str::limit($p->obs, 30) : '—' }}</td>
                            <td>
                                <div class="p-actions">
                                    @can('financeiro.editar')
                                        <button class="btn-ic" onclick="editarPagamento({{ Js::from($p) }})" title="Editar">
                                            <i class="fi fi-rr-pencil"></i>
                                        </button>
                                    @endcan
                                    @can('financeiro.excluir')
                                        <button class="btn-ic danger" onclick="confirmarExclusao({{ $p->id }})"
                                            title="Excluir">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-pag">
            {{ $pagamentos->withQueryString()->links() }}
        </div>
    @endif

    <div class="modal-overlay" id="modalPagamento">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head__title" id="modalTitulo">Novo Registro</div>
            </div>
            <div class="modal-body">
                <div class="field-row">
                    <div>
                        <label class="sdb-label">Cliente <span class="req">*</span></label>
                        <select id="pag-cliente" class="sdb-select">
                            <option value="">Selecione...</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="sdb-label">Tipo <span class="req">*</span></label>
                        <select id="pag-tipo" class="sdb-select">
                            <option value="PAGAMENTO">Pagamento</option>
                            <option value="CREDITO">Crédito</option>
                            <option value="DEBITO">Débito</option>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div>
                        <label class="sdb-label">Valor <span class="req">*</span></label>
                        <input type="number" id="pag-valor" class="sdb-input" step="0.01" min="0.01"
                            placeholder="0,00">
                    </div>
                    <div>
                        <label class="sdb-label">Status <span class="req">*</span></label>
                        <select id="pag-status" class="sdb-select">
                            <option value="PENDENTE">Pendente</option>
                            <option value="PAGO">Pago</option>
                            <option value="ATRASADO">Atrasado</option>
                            <option value="CANCELADO">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div>
                        <label class="sdb-label">Forma de Pagamento</label>
                        <select id="pag-forma" class="sdb-select">
                            <option value="">Selecione...</option>
                            <option value="PIX">PIX</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão Crédito">Cartão Crédito</option>
                            <option value="Cartão Débito">Cartão Débito</option>
                            <option value="Transferência">Transferência</option>
                        </select>
                    </div>
                    <div>
                        <label class="sdb-label">Mês Referência</label>
                        <input type="month" id="pag-ref-mes" class="sdb-input">
                    </div>
                </div>
                <div class="field-row">
                    <div>
                        <label class="sdb-label">Vencimento</label>
                        <input type="date" id="pag-vencimento" class="sdb-input">
                    </div>
                    <div>
                        <label class="sdb-label">Data Pagamento</label>
                        <input type="date" id="pag-data-pgto" class="sdb-input">
                    </div>
                </div>
                <div>
                    <label class="sdb-label">Observações</label>
                    <textarea id="pag-obs" class="sdb-textarea" placeholder="Observações..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button class="btn-save" id="btnSalvar" onclick="salvarPagamento()">Salvar</button>
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-box__title">Confirmar Exclusão</div>
            <div class="confirm-box__msg">Deseja excluir este registro financeiro?</div>
            <div class="confirm-box__actions">
                <button class="btn-cancel" onclick="fecharConfirm()">Cancelar</button>
                <button class="btn-danger" onclick="executarExclusao()"><i class="fi fi-rr-trash"></i> Excluir</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var ROUTES = {
            store: '{{ route('pagamentos.store') }}',
            update: '{{ route('pagamentos.update', ':id') }}',
            destroy: '{{ route('pagamentos.destroy', ':id') }}'
        };

        var modalState = {
            editing: false,
            id: null
        };
        var deleteId = null;

        function abrirModal(pag) {
            modalState.editing = !!pag;
            modalState.id = pag ? pag.id : null;
            document.getElementById('modalTitulo').textContent = pag ? 'Editar Registro' : 'Novo Registro';
            document.getElementById('pag-cliente').value = pag ? pag.cliente_id : '';
            document.getElementById('pag-tipo').value = pag ? pag.tipo : 'PAGAMENTO';
            document.getElementById('pag-valor').value = pag ? pag.valor : '';
            document.getElementById('pag-status').value = pag ? pag.status : 'PENDENTE';
            document.getElementById('pag-forma').value = pag ? (pag.forma_pagamento || '') : '';
            document.getElementById('pag-ref-mes').value = pag ? (pag.referencia_mes || '') : '';
            document.getElementById('pag-vencimento').value = pag ? (pag.data_vencimento || '') : '';
            document.getElementById('pag-data-pgto').value = pag ? (pag.data_pagamento || '') : '';
            document.getElementById('pag-obs').value = pag ? (pag.obs || '') : '';
            document.getElementById('modalPagamento').classList.add('is-open');
        }

        function editarPagamento(pag) {
            abrirModal(pag);
        }

        function fecharModal() {
            document.getElementById('modalPagamento').classList.remove('is-open');
        }

        function salvarPagamento() {
            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

            var payload = {
                cliente_id: parseInt(document.getElementById('pag-cliente').value) || null,
                tipo: document.getElementById('pag-tipo').value,
                valor: parseFloat(document.getElementById('pag-valor').value),
                status: document.getElementById('pag-status').value,
                forma_pagamento: document.getElementById('pag-forma').value || null,
                referencia_mes: document.getElementById('pag-ref-mes').value || null,
                data_vencimento: document.getElementById('pag-vencimento').value || null,
                data_pagamento: document.getElementById('pag-data-pgto').value || null,
                obs: document.getElementById('pag-obs').value.trim() || null
            };

            fetchApi(modalState.editing ? ROUTES.update.replace(':id', modalState.id) : ROUTES.store, {
                    method: modalState.editing ? 'PUT' : 'POST',
                    body: JSON.stringify(payload)
                })
                .then(function() {
                    fecharModal();
                    SdbToast.success(modalState.editing ? 'Registro atualizado' : 'Registro criado');
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    if (err.status === 422) {
                        err.json().then(function(data) {
                            SdbToast.error(Object.values(data.errors || {}).flat().join(', ') || data.message ||
                                'Dados inválidos');
                        });
                    } else if (err.status === 403) {
                        SdbToast.error('Sem permissão para esta ação');
                    } else {
                        SdbToast.error('Erro ao salvar registro');
                    }
                });
        }

        function confirmarExclusao(id) {
            deleteId = id;
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function fecharConfirm() {
            document.getElementById('confirmOverlay').classList.remove('is-open');
            deleteId = null;
        }

        function executarExclusao() {
            if (!deleteId) return;
            fetchApi(ROUTES.destroy.replace(':id', deleteId), {
                    method: 'DELETE'
                })
                .then(function(data) {
                    fecharConfirm();
                    SdbToast.success(data.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                })
                .catch(function(err) {
                    fecharConfirm();
                    if (err.status === 422) {
                        err.json().then(function(data) {
                            SdbToast.error(data.message || 'Erro ao excluir.');
                        });
                    } else {
                        SdbToast.error('Erro ao excluir.');
                    }
                });
        }
    </script>
@endsection
