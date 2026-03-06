@extends('layouts.app')

@section('title', 'Áreas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Tipos de Área</h1>
            <p class="page-header__sub">Gerencie os tipos e suas áreas</p>
        </div>
        @role('admin')
            <button class="btn-primary" onclick="abrirModal()">
                <i class="fi fi-rr-plus"></i> Novo Tipo
            </button>
        @endrole
    </div>
@endsection

@section('styles')
    <style>
        .tp-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .tp-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px;
            cursor: pointer;
            transition: all .18s;
        }

        .tp-card:hover {
            border-color: var(--input-border-h);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
        }

        .tp-card--inactive {
            opacity: .55;
        }

        .tp-card__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .tp-card__icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .tp-card__menu-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: var(--t3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all .15s;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
        }

        .tp-card__menu-btn:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .tp-card__dropdown {
            position: absolute;
            top: 54px;
            right: 16px;
            min-width: 180px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, .22);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: all .15s;
            z-index: 10;
        }

        .tp-card__dropdown.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .tp-card__drop-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 14px;
            border: none;
            background: transparent;
            color: var(--t2);
            font-family: inherit;
            font-size: .84rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all .12s;
            text-align: left;
        }

        .tp-card__drop-item:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .tp-card__drop-item i {
            font-size: .82rem;
            width: 18px;
            text-align: center;
        }

        .tp-card__drop-item--danger {
            color: var(--danger);
        }

        .tp-card__drop-item--danger:hover {
            background: rgba(248, 113, 113, .08);
            color: var(--danger);
        }

        .tp-card__drop-item--warning {
            color: var(--warning);
        }

        .tp-card__drop-item--warning:hover {
            background: rgba(251, 191, 36, .08);
            color: var(--warning);
        }

        .tp-card__drop-item--success {
            color: var(--success);
        }

        .tp-card__drop-item--success:hover {
            background: rgba(52, 211, 153, .08);
            color: var(--success);
        }

        .tp-card__nome {
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 4px;
        }

        .tp-card__status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            margin-bottom: 14px;
        }

        .tp-card__status--ativa {
            background: rgba(52, 211, 153, .12);
            color: var(--success);
        }

        .tp-card__status--inativa {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        .tp-card__status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .tp-card__counters {
            display: flex;
            gap: 10px;
        }

        .tp-card__counter {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--hover);
            border-radius: 10px;
        }

        .tp-card__counter-ic {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            flex-shrink: 0;
        }

        .tp-card__counter-val {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
        }

        .tp-card__counter-lbl {
            font-size: .68rem;
            color: var(--t3);
            font-weight: 500;
        }

        .tp-card__arrow {
            position: absolute;
            bottom: 16px;
            right: 16px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--t4);
            font-size: .72rem;
            transition: all .18s;
        }

        .tp-card:hover .tp-card__arrow {
            color: var(--accent);
            transform: translateX(3px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--t3);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
            opacity: .4;
        }

        .empty-state__text {
            font-size: .92rem;
            margin-bottom: 16px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: all .2s;
            padding: 16px;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(.96);
            transition: transform .2s;
        }

        .modal-overlay.is-open .modal-box {
            transform: scale(1);
        }

        .modal-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--card-border);
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--t1);
        }

        .modal-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modal-foot {
            padding: 14px 22px;
            border-top: 1px solid var(--card-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t2);
            margin-bottom: 6px;
        }

        .sdb-input {
            width: 100%;
            padding: 10px 14px;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            color: var(--t1);
            font-family: inherit;
            font-size: .88rem;
            transition: border-color .15s;
        }

        .sdb-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .icon-opt {
            width: 100%;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            font-size: 1rem;
            cursor: pointer;
            transition: all .12s;
        }

        .icon-opt:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .icon-opt.active {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 6px;
        }

        .color-opt {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all .12s;
        }

        .color-opt:hover {
            transform: scale(1.12);
        }

        .color-opt.active {
            border-color: var(--t1);
            box-shadow: 0 0 0 2px var(--card), 0 0 0 4px currentColor;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 18px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-primary:hover {
            background: var(--accent-h);
        }

        .btn-primary i {
            font-size: .9rem;
        }

        .btn-cancel {
            height: 38px;
            padding: 0 18px;
            background: transparent;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            color: var(--t2);
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-cancel:hover {
            background: var(--hover);
        }

        .btn-save {
            height: 38px;
            padding: 0 18px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-save:hover {
            background: var(--accent-h);
        }

        .btn-save:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 300;
            opacity: 0;
            visibility: hidden;
            transition: all .2s;
            padding: 16px;
        }

        .confirm-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .confirm-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .confirm-box__ic {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin: 0 auto 16px;
        }

        .confirm-box__ic--danger {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        .confirm-box__ic--warning {
            background: rgba(251, 191, 36, .12);
            color: var(--warning);
        }

        .confirm-box__title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 8px;
        }

        .confirm-box__msg {
            font-size: .86rem;
            color: var(--t3);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .confirm-box__actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-danger {
            height: 38px;
            padding: 0 18px;
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .15s;
        }

        .btn-danger:hover {
            opacity: .85;
        }

        .btn-warning {
            height: 38px;
            padding: 0 18px;
            background: var(--warning);
            color: #000;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .15s;
        }

        .btn-warning:hover {
            opacity: .85;
        }

        @media (max-width: 1024px) {
            .tp-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .tp-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .tp-card {
                padding: 16px;
            }

            .tp-card__icon {
                width: 42px;
                height: 42px;
                font-size: 1.15rem;
            }
        }

        @media (max-width: 425px) {
            .tp-card__counters {
                gap: 8px;
            }

            .tp-card__counter {
                padding: 8px 10px;
                gap: 8px;
            }

            .tp-card__counter-val {
                font-size: 1rem;
            }

            .tp-card__counter-ic {
                width: 28px;
                height: 28px;
                font-size: .72rem;
            }

            .modal-box {
                max-width: 100%;
            }

            .icon-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        @media (max-width: 375px) {
            .tp-card {
                padding: 14px;
            }

            .tp-card__nome {
                font-size: .98rem;
            }

            .tp-card__counter-lbl {
                font-size: .64rem;
            }

            .modal-head {
                padding: 14px 16px;
            }

            .modal-body {
                padding: 16px;
            }

            .modal-foot {
                padding: 12px 16px;
            }

            .icon-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 320px) {
            .tp-card__icon {
                width: 38px;
                height: 38px;
                font-size: 1rem;
            }

            .tp-card__counters {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    @if ($tipos->isEmpty())
        <div class="empty-state">
            <i class="fi fi-rr-marker"></i>
            <p class="empty-state__text">Nenhum tipo de área cadastrado</p>
            @role('admin')
                <button class="btn-primary" onclick="abrirModal()">
                    <i class="fi fi-rr-plus"></i> Criar Tipo
                </button>
            @endrole
        </div>
    @else
        <div class="tp-grid">
            @foreach ($tipos as $tipo)
                <div class="tp-card {{ $tipo->ativo ? '' : 'tp-card--inactive' }}"
                    onclick="navegarTipo(event, {{ $tipo->id }})" data-tipo-id="{{ $tipo->id }}">
                    <div class="tp-card__head">
                        <div class="tp-card__icon" style="background:{{ $tipo->cor }}18;color:{{ $tipo->cor }}">
                            <i class="fi {{ $tipo->icone }}"></i>
                        </div>
                        @role('admin')
                            <button class="tp-card__menu-btn" onclick="toggleMenu(event, {{ $tipo->id }})" title="Ações">
                                <i class="fi fi-rr-menu-dots-vertical"></i>
                            </button>
                            <div class="tp-card__dropdown" id="menu-{{ $tipo->id }}">
                                <button class="tp-card__drop-item" onclick="editarTipo(event, {{ Js::from($tipo) }})">
                                    <i class="fi fi-rr-pencil"></i> Editar
                                </button>
                                @if ($tipo->ativo)
                                    <button class="tp-card__drop-item tp-card__drop-item--warning"
                                        onclick="confirmarDesativar(event, {{ $tipo->id }}, '{{ $tipo->nome }}')">
                                        <i class="fi fi-rr-ban"></i> Desativar
                                    </button>
                                @else
                                    <button class="tp-card__drop-item tp-card__drop-item--success"
                                        onclick="ativarTipo(event, {{ $tipo->id }})">
                                        <i class="fi fi-rr-check"></i> Ativar
                                    </button>
                                @endif
                                @if ($tipo->pode_excluir)
                                    <button class="tp-card__drop-item tp-card__drop-item--danger"
                                        onclick="confirmarExcluir(event, {{ $tipo->id }}, '{{ $tipo->nome }}', {{ $tipo->areas_total_count }})">
                                        <i class="fi fi-rr-trash"></i> Excluir
                                    </button>
                                @endif
                            </div>
                        @endrole
                    </div>

                    <div class="tp-card__nome">{{ $tipo->nome }}</div>

                    @if (!$tipo->ativo)
                        <div class="tp-card__status tp-card__status--inativa">
                            <span class="tp-card__status-dot"></span> Inativo
                        </div>
                    @endif

                    <div class="tp-card__counters">
                        <div class="tp-card__counter">
                            <div class="tp-card__counter-ic"
                                style="background:{{ $tipo->cor }}15;color:{{ $tipo->cor }}">
                                <i class="fi fi-rr-marker"></i>
                            </div>
                            <div>
                                <div class="tp-card__counter-val">{{ $tipo->areas_ativas_count }}</div>
                                <div class="tp-card__counter-lbl">{{ $tipo->areas_ativas_count === 1 ? 'Área' : 'Áreas' }}
                                </div>
                            </div>
                        </div>
                        <div class="tp-card__counter">
                            <div class="tp-card__counter-ic" style="background:rgba(167,139,250,.12);color:#a78bfa">
                                <i class="fi fi-rr-calendar-check"></i>
                            </div>
                            <div>
                                <div class="tp-card__counter-val">{{ $tipo->total_reservas }}</div>
                                <div class="tp-card__counter-lbl">
                                    {{ $tipo->total_reservas === 1 ? 'Reserva' : 'Reservas' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="tp-card__arrow">
                        <i class="fi fi-rr-angle-right"></i>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @role('admin')
        <div class="modal-overlay" id="modalTipo">
            <div class="modal-box">
                <div class="modal-head" id="modalTitulo">Novo Tipo de Área</div>
                <div class="modal-body">
                    <div>
                        <label class="sdb-label">Nome</label>
                        <input type="text" id="tipo-nome" class="sdb-input" placeholder="Ex: QUADRA, CHURRASQUEIRA">
                    </div>
                    <div>
                        <label class="sdb-label">Ícone</label>
                        <div class="icon-grid" id="iconGrid"></div>
                    </div>
                    <div>
                        <label class="sdb-label">Cor</label>
                        <div class="color-grid" id="colorGrid"></div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                    <button class="btn-save" id="btnSalvar" onclick="salvarTipo()">Salvar</button>
                </div>
            </div>
        </div>

        <div class="confirm-overlay" id="confirmOverlay">
            <div class="confirm-box">
                <div class="confirm-box__ic" id="confirmIc"></div>
                <div class="confirm-box__title" id="confirmTitulo"></div>
                <div class="confirm-box__msg" id="confirmMsg"></div>
                <div class="confirm-box__actions">
                    <button class="btn-cancel" onclick="fecharConfirm()">Cancelar</button>
                    <button id="confirmBtn" onclick="executarConfirm()"></button>
                </div>
            </div>
        </div>
    @endrole
@endsection

@section('scripts')
    <script>
        var CSRF = '{{ csrf_token() }}';
        var ROUTES = {
            porTipo: '{{ route('areas.porTipo', ':id') }}',
            tipoStore: '{{ route('tipos-area.store') }}',
            tipoUpdate: '{{ route('tipos-area.update', ':id') }}',
            tipoToggle: '{{ route('tipos-area.toggle', ':id') }}',
            tipoDestroy: '{{ route('tipos-area.destroy', ':id') }}'
        };

        var ICONES = [{
                id: 'fi-rr-football',
                label: 'Futebol'
            },
            {
                id: 'fi-rr-basketball',
                label: 'Basquete'
            },
            {
                id: 'fi-rr-baseball',
                label: 'Baseball'
            },
            {
                id: 'fi-rr-table-tennis',
                label: 'Ping-pong'
            },
            {
                id: 'fi-rr-trophy',
                label: 'Troféu'
            },
            {
                id: 'fi-rr-grill',
                label: 'Churrasqueira'
            },
            {
                id: 'fi-rr-flame',
                label: 'Fogo'
            },
            {
                id: 'fi-rr-marker',
                label: 'Local'
            },
            {
                id: 'fi-rr-home',
                label: 'Casa'
            },
            {
                id: 'fi-rr-building',
                label: 'Prédio'
            },
            {
                id: 'fi-rr-flag',
                label: 'Bandeira'
            },
            {
                id: 'fi-rr-target',
                label: 'Alvo'
            },
            {
                id: 'fi-rr-swimming-pool',
                label: 'Piscina'
            },
            {
                id: 'fi-rr-star',
                label: 'Estrela'
            }
        ];

        var CORES = [
            '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7',
            '#ec4899', '#f43f5e', '#ef4444', '#f97316',
            '#f59e0b', '#eab308', '#84cc16', '#22c55e',
            '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9'
        ];

        var modalState = {
            editing: false,
            id: null,
            icone: null,
            cor: null
        };
        var confirmCallback = null;

        (function init() {
            var iconGrid = document.getElementById('iconGrid');
            if (!iconGrid) return;
            var ih = '';
            for (var i = 0; i < ICONES.length; i++) {
                ih += '<button type="button" class="icon-opt" data-icon="' + ICONES[i].id +
                    '" title="' + ICONES[i].label + '" onclick="selecionarIcone(this)">' +
                    '<i class="fi ' + ICONES[i].id + '"></i></button>';
            }
            iconGrid.innerHTML = ih;

            var colorGrid = document.getElementById('colorGrid');
            var ch = '';
            for (var j = 0; j < CORES.length; j++) {
                ch += '<button type="button" class="color-opt" data-cor="' + CORES[j] +
                    '" style="background:' + CORES[j] + '" title="' + CORES[j] +
                    '" onclick="selecionarCor(this)"></button>';
            }
            colorGrid.innerHTML = ch;
        })();

        function navegarTipo(e, id) {
            if (e.target.closest('.tp-card__menu-btn') || e.target.closest('.tp-card__dropdown')) return;
            window.location.href = ROUTES.porTipo.replace(':id', id);
        }

        function toggleMenu(e, id) {
            e.stopPropagation();
            var menu = document.getElementById('menu-' + id);
            var aberto = menu.classList.contains('is-open');
            fecharMenus();
            if (!aberto) menu.classList.add('is-open');
        }

        function fecharMenus() {
            document.querySelectorAll('.tp-card__dropdown.is-open').forEach(function(m) {
                m.classList.remove('is-open');
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.tp-card__menu-btn') && !e.target.closest('.tp-card__dropdown')) {
                fecharMenus();
            }
        });

        function selecionarIcone(el) {
            document.querySelectorAll('.icon-opt').forEach(function(o) {
                o.classList.remove('active');
            });
            el.classList.add('active');
            modalState.icone = el.dataset.icon;
        }

        function selecionarCor(el) {
            document.querySelectorAll('.color-opt').forEach(function(o) {
                o.classList.remove('active');
            });
            el.classList.add('active');
            modalState.cor = el.dataset.cor;
        }

        function abrirModal(tipo) {
            modalState.editing = !!tipo;
            modalState.id = tipo ? tipo.id : null;
            modalState.icone = tipo ? tipo.icone : null;
            modalState.cor = tipo ? tipo.cor : null;

            document.getElementById('modalTitulo').textContent = tipo ? 'Editar Tipo' : 'Novo Tipo de Área';
            document.getElementById('tipo-nome').value = tipo ? tipo.nome : '';

            document.querySelectorAll('.icon-opt').forEach(function(o) {
                o.classList.toggle('active', tipo && o.dataset.icon === tipo.icone);
            });
            document.querySelectorAll('.color-opt').forEach(function(o) {
                o.classList.toggle('active', tipo && o.dataset.cor === tipo.cor);
            });

            document.getElementById('modalTipo').classList.add('is-open');
        }

        function fecharModal() {
            document.getElementById('modalTipo').classList.remove('is-open');
        }

        function editarTipo(e, tipo) {
            e.stopPropagation();
            fecharMenus();
            abrirModal(tipo);
        }

        function salvarTipo() {
            var nome = document.getElementById('tipo-nome').value.trim().toUpperCase();
            if (!nome) {
                SdbToast.error('Informe o nome do tipo.');
                return;
            }
            if (!modalState.icone) {
                SdbToast.error('Selecione um ícone.');
                return;
            }
            if (!modalState.cor) {
                SdbToast.error('Selecione uma cor.');
                return;
            }

            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

            var url = modalState.editing ?
                ROUTES.tipoUpdate.replace(':id', modalState.id) :
                ROUTES.tipoStore;

            fetch(url, {
                    method: modalState.editing ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nome: nome,
                        icone: modalState.icone,
                        cor: modalState.cor
                    })
                })
                .then(function(r) {
                    if (!r.ok) return r.json().then(function(e) {
                        throw e;
                    });
                    return r.json();
                })
                .then(function() {
                    fecharModal();
                    SdbToast.success(modalState.editing ? 'Tipo atualizado.' : 'Tipo criado.');
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    var msg = err.message || 'Erro ao salvar tipo.';
                    if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                    SdbToast.error(msg);
                });
        }

        function confirmarDesativar(e, id, nome) {
            e.stopPropagation();
            fecharMenus();
            var ic = document.getElementById('confirmIc');
            ic.className = 'confirm-box__ic confirm-box__ic--warning';
            ic.innerHTML = '<i class="fi fi-rr-ban"></i>';
            document.getElementById('confirmTitulo').textContent = 'Desativar Tipo';
            document.getElementById('confirmMsg').textContent = 'O tipo "' + nome +
                '" será desativado e não aparecerá para usuários comuns. Você pode reativá-lo a qualquer momento.';
            var btn = document.getElementById('confirmBtn');
            btn.className = 'btn-warning';
            btn.innerHTML = '<i class="fi fi-rr-ban"></i> Desativar';
            confirmCallback = function() {
                executarToggle(id);
            };
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function ativarTipo(e, id) {
            e.stopPropagation();
            fecharMenus();
            executarToggle(id);
        }

        function executarToggle(id) {
            fetch(ROUTES.tipoToggle.replace(':id', id), {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) {
                    if (!r.ok) return r.json().then(function(e) {
                        throw e;
                    });
                    return r.json();
                })
                .then(function(data) {
                    fecharConfirm();
                    SdbToast.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                })
                .catch(function(err) {
                    SdbToast.error(err.message || 'Erro ao alterar status.');
                });
        }

        function confirmarExcluir(e, id, nome, totalAreas) {
            e.stopPropagation();
            fecharMenus();
            var ic = document.getElementById('confirmIc');
            ic.className = 'confirm-box__ic confirm-box__ic--danger';
            ic.innerHTML = '<i class="fi fi-rr-trash"></i>';
            document.getElementById('confirmTitulo').textContent = 'Excluir Tipo';
            var msg = 'O tipo "' + nome + '" será excluído permanentemente.';
            if (totalAreas > 0) {
                msg += ' Isso também removerá ' + totalAreas + (totalAreas === 1 ? ' área vinculada' :
                    ' áreas vinculadas') + ' e todos os seus dados (horários, valores, dias).';
            }
            msg += ' Esta ação não pode ser desfeita.';
            document.getElementById('confirmMsg').textContent = msg;
            var btn = document.getElementById('confirmBtn');
            btn.className = 'btn-danger';
            btn.innerHTML = '<i class="fi fi-rr-trash"></i> Excluir';
            confirmCallback = function() {
                executarExcluir(id);
            };
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function executarExcluir(id) {
            fetch(ROUTES.tipoDestroy.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) {
                    if (!r.ok) return r.json().then(function(e) {
                        throw e;
                    });
                    return r.json();
                })
                .then(function(data) {
                    fecharConfirm();
                    SdbToast.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                })
                .catch(function(err) {
                    SdbToast.error(err.message || 'Erro ao excluir.');
                });
        }

        function executarConfirm() {
            if (confirmCallback) confirmCallback();
        }

        function fecharConfirm() {
            document.getElementById('confirmOverlay').classList.remove('is-open');
            confirmCallback = null;
        }
    </script>
@endsection
