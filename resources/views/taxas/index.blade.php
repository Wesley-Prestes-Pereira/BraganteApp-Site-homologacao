@extends('layouts.app')

@section('title', 'Taxas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Taxas</h1>
            <p class="page-header__sub">Gerencie as taxas aplicáveis às áreas e reservas</p>
        </div>
        @can('taxas.criar')
            <button class="act-btn" onclick="abrirModal()"><i class="fi fi-rr-plus"></i> Nova Taxa</button>
        @endcan
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
            transition: background .15s;
        }

        .act-btn:hover {
            background: var(--accent-h);
        }

        .t-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .t-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .t-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .t-stat__val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--t1);
        }

        .t-stat__lbl {
            font-size: .78rem;
            color: var(--t3);
            margin-top: 2px;
        }

        .t-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 14px;
        }

        .t-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px;
            transition: border-color .2s;
        }

        .t-card:hover {
            border-color: var(--input-border-h);
        }

        .t-card--inactive {
            opacity: .55;
        }

        .t-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .t-card__nome {
            font-size: .95rem;
            font-weight: 700;
            color: var(--t1);
        }

        .t-card__badge {
            font-size: .68rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .t-card__badge--fixo {
            background: rgba(52, 211, 153, .12);
            color: var(--success);
        }

        .t-card__badge--percentual {
            background: rgba(167, 139, 250, .12);
            color: var(--purple);
        }

        .t-card__valor {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 14px;
        }

        .t-card__footer {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            border-top: 1px solid var(--card-border);
            padding-top: 12px;
        }

        .btn-ic {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            cursor: pointer;
            font-size: .82rem;
            transition: all .15s;
        }

        .btn-ic:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .btn-ic.warning:hover {
            color: var(--warning);
            border-color: var(--warning);
        }

        .btn-ic.success:hover {
            color: var(--success);
            border-color: var(--success);
        }

        .btn-ic.danger:hover {
            color: var(--danger);
            border-color: var(--danger);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--t4);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.is-open {
            display: flex;
        }

        .modal-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
        }

        .modal-head {
            padding: 20px 24px 0;
        }

        .modal-head__title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--t1);
        }

        .modal-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t3);
            margin-bottom: 6px;
        }

        .req {
            color: var(--danger);
        }

        .sdb-input,
        .sdb-select {
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
            transition: border-color .15s, box-shadow .15s;
        }

        .sdb-input:focus,
        .sdb-select:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        .modal-foot {
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid var(--card-border);
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
            cursor: pointer;
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
            cursor: pointer;
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .confirm-overlay.is-open {
            display: flex;
        }

        .confirm-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            max-width: 380px;
            width: 100%;
        }

        .confirm-box__title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 8px;
        }

        .confirm-box__msg {
            font-size: .84rem;
            color: var(--t3);
            margin-bottom: 20px;
        }

        .confirm-box__actions {
            display: flex;
            gap: 8px;
            justify-content: center;
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
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .t-stats {
                grid-template-columns: 1fr;
            }

            .t-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="t-stats">
        <div class="t-stat">
            <div class="t-stat__ic" style="background:rgba(91,156,246,.15);color:var(--accent)"><i
                    class="fi fi-rr-dollar"></i></div>
            <div>
                <div class="t-stat__val">{{ $total }}</div>
                <div class="t-stat__lbl">Total</div>
            </div>
        </div>
        <div class="t-stat">
            <div class="t-stat__ic" style="background:rgba(52,211,153,.15);color:var(--success)"><i
                    class="fi fi-rr-check-circle"></i></div>
            <div>
                <div class="t-stat__val">{{ $ativas }}</div>
                <div class="t-stat__lbl">Ativas</div>
            </div>
        </div>
        <div class="t-stat">
            <div class="t-stat__ic" style="background:rgba(248,113,113,.15);color:var(--danger)"><i
                    class="fi fi-rr-ban"></i></div>
            <div>
                <div class="t-stat__val">{{ $inativas }}</div>
                <div class="t-stat__lbl">Inativas</div>
            </div>
        </div>
    </div>

    @if ($taxas->isEmpty())
        <div class="empty-state">
            <i class="fi fi-rr-dollar"></i>
            <p>Nenhuma taxa cadastrada</p>
        </div>
    @else
        <div class="t-grid">
            @foreach ($taxas as $taxa)
                <div class="t-card {{ $taxa->ativo ? '' : 't-card--inactive' }}">
                    <div class="t-card__top">
                        <div class="t-card__nome">{{ $taxa->nome }}</div>
                        <span
                            class="t-card__badge t-card__badge--{{ strtolower($taxa->tipo_cobranca) }}">{{ $taxa->tipo_cobranca }}</span>
                    </div>
                    <div class="t-card__valor">
                        @if ($taxa->tipo_cobranca === 'PERCENTUAL')
                            {{ number_format((float) $taxa->valor, 2, ',', '.') }}%
                        @else
                            R$ {{ number_format((float) $taxa->valor, 2, ',', '.') }}
                        @endif
                    </div>
                    <div class="t-card__footer">
                        @can('taxas.editar')
                            <button class="btn-ic {{ $taxa->ativo ? 'warning' : 'success' }}"
                                onclick="toggleStatus({{ $taxa->id }})"
                                title="{{ $taxa->ativo ? 'Desativar' : 'Ativar' }}">
                                <i class="fi fi-rr-{{ $taxa->ativo ? 'ban' : 'check' }}"></i>
                            </button>
                            <button class="btn-ic" onclick="editarTaxa({{ Js::from($taxa) }})" title="Editar">
                                <i class="fi fi-rr-pencil"></i>
                            </button>
                        @endcan
                        @can('taxas.excluir')
                            <button class="btn-ic danger"
                                onclick="confirmarExclusao({{ $taxa->id }}, '{{ $taxa->nome }}')" title="Excluir">
                                <i class="fi fi-rr-trash"></i>
                            </button>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="modal-overlay" id="modalTaxa">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head__title" id="modalTitulo">Nova Taxa</div>
            </div>
            <div class="modal-body">
                <div>
                    <label class="sdb-label">Nome <span class="req">*</span></label>
                    <input type="text" id="taxa-nome" class="sdb-input" placeholder="Ex: Iluminação">
                </div>
                <div>
                    <label class="sdb-label">Valor <span class="req">*</span></label>
                    <input type="number" id="taxa-valor" class="sdb-input" step="0.01" min="0.01"
                        placeholder="0,00">
                </div>
                <div>
                    <label class="sdb-label">Tipo de Cobrança <span class="req">*</span></label>
                    <select id="taxa-tipo" class="sdb-select">
                        <option value="FIXO">Valor Fixo (R$)</option>
                        <option value="PERCENTUAL">Percentual (%)</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button class="btn-save" id="btnSalvar" onclick="salvarTaxa()">Salvar</button>
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-box__title">Confirmar Exclusão</div>
            <div class="confirm-box__msg" id="confirmMsg"></div>
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
            store: '{{ route('taxas.store') }}',
            update: '{{ route('taxas.update', ':id') }}',
            toggle: '{{ route('taxas.toggle', ':id') }}',
            destroy: '{{ route('taxas.destroy', ':id') }}'
        };

        var modalState = {
            editing: false,
            id: null
        };
        var deleteId = null;

        function abrirModal(taxa) {
            modalState.editing = !!taxa;
            modalState.id = taxa ? taxa.id : null;
            document.getElementById('modalTitulo').textContent = taxa ? 'Editar Taxa' : 'Nova Taxa';
            document.getElementById('taxa-nome').value = taxa ? taxa.nome : '';
            document.getElementById('taxa-valor').value = taxa ? taxa.valor : '';
            document.getElementById('taxa-tipo').value = taxa ? taxa.tipo_cobranca : 'FIXO';
            document.getElementById('modalTaxa').classList.add('is-open');
        }

        function editarTaxa(taxa) {
            abrirModal(taxa);
        }

        function fecharModal() {
            document.getElementById('modalTaxa').classList.remove('is-open');
        }

        function salvarTaxa() {
            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

            var payload = {
                nome: document.getElementById('taxa-nome').value.trim(),
                valor: parseFloat(document.getElementById('taxa-valor').value),
                tipo_cobranca: document.getElementById('taxa-tipo').value
            };

            fetchApi(modalState.editing ? ROUTES.update.replace(':id', modalState.id) : ROUTES.store, {
                    method: modalState.editing ? 'PUT' : 'POST',
                    body: JSON.stringify(payload)
                })
                .then(function() {
                    fecharModal();
                    SdbToast.success(modalState.editing ? 'Taxa atualizada' : 'Taxa criada');
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
                        SdbToast.error('Erro ao salvar taxa');
                    }
                });
        }

        function toggleStatus(id) {
            fetchApi(ROUTES.toggle.replace(':id', id), {
                    method: 'PATCH'
                })
                .then(function(data) {
                    SdbToast.success(data.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 600);
                })
                .catch(function(err) {
                    if (err.status === 422) {
                        err.json().then(function(data) {
                            SdbToast.error(data.message || 'Erro ao alterar status.');
                        });
                    } else {
                        SdbToast.error('Erro ao alterar status.');
                    }
                });
        }

        function confirmarExclusao(id, nome) {
            deleteId = id;
            document.getElementById('confirmMsg').textContent = 'Deseja excluir a taxa "' + nome + '"?';
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
