@extends('layouts.app')

@section('title', 'Clientes')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Clientes</h1>
            <p class="page-header__sub">Gerencie os clientes do complexo</p>
        </div>
        <div style="display:flex;gap:6px;">
            @can('clientes.ver')
                <a href="{{ route('clientes.exportar.xlsx') }}" class="act-btn-label">
                    <i class="fi fi-rr-download"></i> Excel
                </a>
            @endcan
            @can('clientes.criar')
                <button class="act-btn" onclick="abrirModal()"><i class="fi fi-rr-plus"></i> Novo Cliente</button>
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
            transition: background .15s;
        }

        .act-btn:hover {
            background: var(--accent-h);
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
            transition: all .2s;
        }

        .act-btn-label:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
        }

        .c-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .c-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .c-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .c-stat__val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--t1);
        }

        .c-stat__lbl {
            font-size: .78rem;
            color: var(--t3);
            margin-top: 2px;
        }

        .c-search {
            margin-bottom: 16px;
        }

        .c-search__input {
            width: 100%;
            max-width: 400px;
            height: 42px;
            padding: 0 14px 0 40px;
            border-radius: 10px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--t1);
            font-size: .88rem;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .c-search__input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        .c-search__wrap {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 400px;
        }

        .c-search__icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--t4);
            font-size: .88rem;
        }

        .c-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 14px;
        }

        .c-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px;
            transition: border-color .2s;
        }

        .c-card:hover {
            border-color: var(--input-border-h);
        }

        .c-card--inactive {
            opacity: .55;
        }

        .c-card__top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .c-card__avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .c-card__nome {
            font-size: .95rem;
            font-weight: 700;
            color: var(--t1);
        }

        .c-card__sub {
            font-size: .76rem;
            color: var(--t3);
        }

        .c-card__info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .c-card__row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            color: var(--t2);
        }

        .c-card__row i {
            font-size: .78rem;
            color: var(--t4);
            width: 16px;
            text-align: center;
        }

        .c-card__footer {
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

        .c-card__reservas {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: .76rem;
            color: var(--t3);
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
            max-width: 500px;
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

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
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
            transition: border-color .15s, box-shadow .15s;
        }

        .sdb-textarea {
            height: 80px;
            padding: 10px 14px;
            resize: vertical;
        }

        .sdb-input:focus,
        .sdb-textarea:focus {
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
            .c-stats {
                grid-template-columns: 1fr;
            }

            .c-grid {
                grid-template-columns: 1fr;
            }

            .field-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="c-stats">
        <div class="c-stat">
            <div class="c-stat__ic" style="background:rgba(91,156,246,.15);color:var(--accent)"><i
                    class="fi fi-rr-users"></i></div>
            <div>
                <div class="c-stat__val">{{ $totalClientes }}</div>
                <div class="c-stat__lbl">Total</div>
            </div>
        </div>
        <div class="c-stat">
            <div class="c-stat__ic" style="background:rgba(52,211,153,.15);color:var(--success)"><i
                    class="fi fi-rr-check-circle"></i></div>
            <div>
                <div class="c-stat__val">{{ $ativos }}</div>
                <div class="c-stat__lbl">Ativos</div>
            </div>
        </div>
        <div class="c-stat">
            <div class="c-stat__ic" style="background:rgba(248,113,113,.15);color:var(--danger)"><i
                    class="fi fi-rr-ban"></i></div>
            <div>
                <div class="c-stat__val">{{ $inativos }}</div>
                <div class="c-stat__lbl">Inativos</div>
            </div>
        </div>
    </div>

    <div class="c-search">
        <div class="c-search__wrap">
            <i class="fi fi-rr-search c-search__icon"></i>
            <input type="text" class="c-search__input" id="buscaCliente"
                placeholder="Buscar por nome, telefone, email..." oninput="filtrarClientes()">
        </div>
    </div>

    @if ($clientes->isEmpty())
        <div class="empty-state">
            <i class="fi fi-rr-users"></i>
            <p>Nenhum cliente cadastrado</p>
        </div>
    @else
        <div class="c-grid" id="clientesGrid">
            @foreach ($clientes as $cliente)
                <div class="c-card {{ $cliente->ativo ? '' : 'c-card--inactive' }}"
                    data-search="{{ strtolower($cliente->nome . ' ' . $cliente->telefone . ' ' . $cliente->email . ' ' . $cliente->cpf) }}">
                    <div class="c-card__top">
                        <div class="c-card__avatar">{{ strtoupper(substr($cliente->nome, 0, 2)) }}</div>
                        <div>
                            <div class="c-card__nome">{{ $cliente->nome }}</div>
                            <div class="c-card__sub">
                                <span class="c-card__reservas">
                                    <i class="fi fi-rr-list"></i>
                                    {{ $cliente->reservas_count }}
                                    {{ $cliente->reservas_count === 1 ? 'reserva' : 'reservas' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="c-card__info">
                        @if ($cliente->telefone)
                            <div class="c-card__row"><i class="fi fi-rr-phone-call"></i> {{ $cliente->telefone }}</div>
                        @endif
                        @if ($cliente->email)
                            <div class="c-card__row"><i class="fi fi-rr-envelope"></i> {{ $cliente->email }}</div>
                        @endif
                        @if ($cliente->cpf)
                            <div class="c-card__row"><i class="fi fi-rr-id-badge"></i> {{ $cliente->cpf }}</div>
                        @endif
                    </div>
                    <div class="c-card__footer">
                        @can('clientes.editar')
                            <button class="btn-ic {{ $cliente->ativo ? 'warning' : 'success' }}"
                                onclick="toggleStatus({{ $cliente->id }})"
                                title="{{ $cliente->ativo ? 'Desativar' : 'Ativar' }}">
                                <i class="fi fi-rr-{{ $cliente->ativo ? 'ban' : 'check' }}"></i>
                            </button>
                            <button class="btn-ic" onclick="editarCliente({{ Js::from($cliente) }})" title="Editar">
                                <i class="fi fi-rr-pencil"></i>
                            </button>
                        @endcan
                        @can('clientes.excluir')
                            <button class="btn-ic danger"
                                onclick="confirmarExclusao({{ $cliente->id }}, '{{ $cliente->nome }}')" title="Excluir">
                                <i class="fi fi-rr-trash"></i>
                            </button>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="modal-overlay" id="modalCliente">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head__title" id="modalTitulo">Novo Cliente</div>
            </div>
            <div class="modal-body">
                <div>
                    <label class="sdb-label">Nome <span class="req">*</span></label>
                    <input type="text" id="cli-nome" class="sdb-input" placeholder="Nome completo">
                </div>
                <div class="field-row">
                    <div>
                        <label class="sdb-label">Telefone</label>
                        <input type="text" id="cli-telefone" class="sdb-input" placeholder="99999-9999">
                    </div>
                    <div>
                        <label class="sdb-label">CPF</label>
                        <input type="text" id="cli-cpf" class="sdb-input" placeholder="000.000.000-00">
                    </div>
                </div>
                <div>
                    <label class="sdb-label">Email</label>
                    <input type="email" id="cli-email" class="sdb-input" placeholder="email@exemplo.com">
                </div>
                <div>
                    <label class="sdb-label">Observações</label>
                    <textarea id="cli-obs" class="sdb-textarea" placeholder="Observações sobre o cliente..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button class="btn-save" id="btnSalvar" onclick="salvarCliente()">Salvar</button>
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
            store: '{{ route('clientes.store') }}',
            update: '{{ route('clientes.update', ':id') }}',
            toggle: '{{ route('clientes.toggle', ':id') }}',
            destroy: '{{ route('clientes.destroy', ':id') }}'
        };

        var modalState = {
            editing: false,
            id: null
        };
        var deleteId = null;

        function abrirModal(cliente) {
            modalState.editing = !!cliente;
            modalState.id = cliente ? cliente.id : null;
            document.getElementById('modalTitulo').textContent = cliente ? 'Editar Cliente' : 'Novo Cliente';
            document.getElementById('cli-nome').value = cliente ? cliente.nome : '';
            document.getElementById('cli-telefone').value = cliente ? (cliente.telefone || '') : '';
            document.getElementById('cli-email').value = cliente ? (cliente.email || '') : '';
            document.getElementById('cli-cpf').value = cliente ? (cliente.cpf || '') : '';
            document.getElementById('cli-obs').value = cliente ? (cliente.obs || '') : '';
            document.getElementById('modalCliente').classList.add('is-open');
        }

        function editarCliente(cliente) {
            abrirModal(cliente);
        }

        function fecharModal() {
            document.getElementById('modalCliente').classList.remove('is-open');
        }

        function salvarCliente() {
            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

            var payload = {
                nome: document.getElementById('cli-nome').value.trim(),
                telefone: document.getElementById('cli-telefone').value.trim() || null,
                email: document.getElementById('cli-email').value.trim() || null,
                cpf: document.getElementById('cli-cpf').value.trim() || null,
                obs: document.getElementById('cli-obs').value.trim() || null
            };

            fetchApi(modalState.editing ? ROUTES.update.replace(':id', modalState.id) : ROUTES.store, {
                    method: modalState.editing ? 'PUT' : 'POST',
                    body: JSON.stringify(payload)
                })
                .then(function() {
                    fecharModal();
                    SdbToast.success(modalState.editing ? 'Cliente atualizado' : 'Cliente cadastrado');
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
                        SdbToast.error('Erro ao salvar cliente');
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
            document.getElementById('confirmMsg').textContent = 'Deseja excluir o cliente "' + nome + '"?';
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

        function filtrarClientes() {
            var termo = document.getElementById('buscaCliente').value.toLowerCase();
            var cards = document.querySelectorAll('#clientesGrid .c-card');
            cards.forEach(function(card) {
                var match = card.getAttribute('data-search').indexOf(termo) !== -1;
                card.style.display = match ? '' : 'none';
            });
        }
    </script>
@endsection
