@extends('layouts.app')

@section('title', $tipo->nome . ' — Áreas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div class="breadcrumb">
                <a href="{{ route('areas.index') }}" class="breadcrumb__link">Áreas</a>
                <i class="fi fi-rr-angle-right breadcrumb__sep"></i>
                <span class="breadcrumb__current">
                    <i class="fi {{ $tipo->icone }}" style="color:{{ $tipo->cor }}"></i>
                    {{ $tipo->nome }}
                </span>
            </div>
            <p class="page-header__sub">{{ $areas->count() }}
                {{ $areas->count() === 1 ? 'área cadastrada' : 'áreas cadastradas' }}</p>
        </div>
        @can('areas.criar')
            <button class="btn-primary" onclick="abrirModal()">
                <i class="fi fi-rr-plus"></i> Nova Área
            </button>
        @endcan
    </div>
@endsection

@section('styles')
    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--t1);
        }

        .breadcrumb__link {
            color: var(--t3);
            text-decoration: none;
            transition: color .15s;
        }

        .breadcrumb__link:hover {
            color: var(--accent);
        }

        .breadcrumb__sep {
            font-size: .65rem;
            color: var(--t4);
        }

        .breadcrumb__current {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ar-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .ar-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px;
            cursor: pointer;
            transition: all .18s;
        }

        .ar-card:hover {
            border-color: var(--input-border-h);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
        }

        .ar-card--inactive {
            opacity: .55;
        }

        .ar-card__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .ar-card__info {
            flex: 1;
            min-width: 0;
        }

        .ar-card__nome {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ar-card__status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .ar-card__status--ativa {
            background: rgba(52, 211, 153, .12);
            color: var(--success);
        }

        .ar-card__status--inativa {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        .ar-card__status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .ar-card__menu-btn {
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

        .ar-card__menu-btn:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .ar-card__dropdown {
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

        .ar-card__dropdown.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .ar-card__drop-item {
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

        .ar-card__drop-item:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .ar-card__drop-item i {
            font-size: .82rem;
            width: 18px;
            text-align: center;
        }

        .ar-card__drop-item--danger {
            color: var(--danger);
        }

        .ar-card__drop-item--danger:hover {
            background: rgba(248, 113, 113, .08);
            color: var(--danger);
        }

        .ar-card__drop-item--warning {
            color: var(--warning);
        }

        .ar-card__drop-item--warning:hover {
            background: rgba(251, 191, 36, .08);
        }

        .ar-card__drop-item--success {
            color: var(--success);
        }

        .ar-card__drop-item--success:hover {
            background: rgba(52, 211, 153, .08);
        }

        .ar-card__details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 14px;
        }

        .ar-card__row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            color: var(--t3);
        }

        .ar-card__row i {
            font-size: .72rem;
            width: 16px;
            text-align: center;
            color: var(--t4);
        }

        .ar-card__dias {
            display: flex;
            gap: 3px;
        }

        .ar-dia {
            width: 28px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-size: .58rem;
            font-weight: 700;
            color: var(--t4);
            background: var(--hover);
            transition: all .15s;
        }

        .ar-dia--active {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        [data-theme="light"] .ar-dia--active {
            background: rgba(59, 130, 246, .10);
        }

        .ar-card__reservas {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: .72rem;
            font-weight: 600;
            background: rgba(167, 139, 250, .12);
            color: #a78bfa;
        }

        [data-theme="light"] .ar-card__reservas {
            background: rgba(139, 92, 246, .08);
        }

        .ar-card__reservas i {
            font-size: .62rem;
        }

        .ar-card__arrow {
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

        .ar-card:hover .ar-card__arrow {
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
            max-width: 520px;
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

        .sdb-input,
        .sdb-select,
        .sdb-textarea {
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

        .sdb-input:focus,
        .sdb-select:focus,
        .sdb-textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        .sdb-textarea {
            resize: vertical;
            min-height: 70px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .dias-grid {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .dia-check {
            flex: 1;
            min-width: 42px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 4px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: transparent;
            cursor: pointer;
            transition: all .15s;
        }

        .dia-check:hover {
            border-color: var(--accent);
        }

        .dia-check.active {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .10);
        }

        .dia-check__box {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 2px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .55rem;
            color: transparent;
            transition: all .15s;
        }

        .dia-check.active .dia-check__box {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .dia-check__abbr {
            font-size: .68rem;
            font-weight: 700;
            color: var(--t3);
        }

        .dia-check.active .dia-check__abbr {
            color: var(--accent);
        }

        .horarios-section {
            display: none;
        }

        .horarios-section.is-visible {
            display: block;
        }

        .horarios-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }

        .horarios-row .sdb-input {
            flex: 1;
        }

        .btn-add-horario {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: transparent;
            border: 1px dashed var(--card-border);
            border-radius: 8px;
            color: var(--accent);
            font-family: inherit;
            font-size: .8rem;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-add-horario:hover {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .05);
        }

        .btn-rm-horario {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: var(--danger);
            border-radius: 8px;
            cursor: pointer;
            font-size: .78rem;
        }

        .btn-rm-horario:hover {
            background: rgba(248, 113, 113, .08);
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

        .confirm-box__ic--info {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
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
            .ar-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .ar-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .ar-card {
                padding: 16px;
            }
        }

        @media (max-width: 425px) {
            .breadcrumb {
                font-size: .98rem;
            }

            .modal-box {
                max-width: 100%;
            }

            .field-row {
                grid-template-columns: 1fr;
            }

            .dias-grid {
                gap: 4px;
            }

            .dia-check {
                padding: 8px 2px;
                min-width: 36px;
            }

            .dia-check__abbr {
                font-size: .6rem;
            }
        }

        @media (max-width: 375px) {
            .breadcrumb {
                font-size: .92rem;
                gap: 6px;
            }

            .ar-card {
                padding: 14px;
            }

            .ar-card__nome {
                font-size: .94rem;
            }

            .modal-head {
                padding: 14px 16px;
            }

            .modal-body {
                padding: 16px;
                gap: 14px;
            }

            .modal-foot {
                padding: 12px 16px;
            }

            .ar-dia {
                width: 24px;
                height: 20px;
                font-size: .52rem;
            }
        }

        @media (max-width: 320px) {
            .breadcrumb {
                font-size: .86rem;
            }

            .ar-card__details {
                gap: 6px;
            }

            .ar-card__row {
                font-size: .78rem;
            }
        }
    </style>
@endsection

@section('content')
    @if ($areas->isEmpty())
        <div class="empty-state">
            <i class="fi {{ $tipo->icone }}" style="color:{{ $tipo->cor }}"></i>
            <p class="empty-state__text">Nenhuma área cadastrada para {{ $tipo->nome }}</p>
            @can('areas.criar')
                <button class="btn-primary" onclick="abrirModal()">
                    <i class="fi fi-rr-plus"></i> Nova Área
                </button>
            @endcan
        </div>
    @else
        <div class="ar-grid">
            @foreach ($areas as $area)
                <div class="ar-card {{ $area->ativo ? '' : 'ar-card--inactive' }}"
                    onclick="navegarArea(event, {{ $area->id }})" data-area-id="{{ $area->id }}">
                    <div class="ar-card__head">
                        <div class="ar-card__info">
                            <div class="ar-card__nome">{{ $area->nome }}</div>
                            <div class="ar-card__status ar-card__status--{{ $area->ativo ? 'ativa' : 'inativa' }}">
                                <span class="ar-card__status-dot"></span>
                                {{ $area->ativo ? 'Ativa' : 'Inativa' }}
                            </div>
                        </div>
                        @if ($isAdmin || auth()->user()->can('areas.editar'))
                            <button class="ar-card__menu-btn" onclick="toggleMenu(event, {{ $area->id }})"
                                title="Ações">
                                <i class="fi fi-rr-menu-dots-vertical"></i>
                            </button>
                            <div class="ar-card__dropdown" id="menu-{{ $area->id }}">
                                @can('areas.editar')
                                    <button class="ar-card__drop-item"
                                        onclick="editarArea(event, {{ Js::from([
                                            'id' => $area->id,
                                            'nome' => $area->nome,
                                            'descricao' => $area->descricao,
                                            'capacidade_pessoas' => $area->capacidade_pessoas,
                                            'modo_reserva' => $area->modo_reserva,
                                            'dias_lista' => $area->dias_lista,
                                        ]) }})">
                                        <i class="fi fi-rr-pencil"></i> Editar
                                    </button>
                                    @if ($area->ativo)
                                        <button class="ar-card__drop-item ar-card__drop-item--warning"
                                            onclick="confirmarDesativar(event, {{ $area->id }}, {{ Js::from($area->nome) }})">
                                            <i class="fi fi-rr-ban"></i> Desativar
                                        </button>
                                    @else
                                        <button class="ar-card__drop-item ar-card__drop-item--success"
                                            onclick="ativarArea(event, {{ $area->id }})">
                                            <i class="fi fi-rr-check"></i> Ativar
                                        </button>
                                    @endif
                                @endcan
                                @role('admin')
                                    @if ($area->pode_excluir)
                                        <button class="ar-card__drop-item ar-card__drop-item--danger"
                                            onclick="confirmarExcluir(event, {{ $area->id }}, {{ Js::from($area->nome) }})">
                                            <i class="fi fi-rr-trash"></i> Excluir
                                        </button>
                                    @else
                                        <button class="ar-card__drop-item" disabled style="opacity:.4;cursor:not-allowed"
                                            title="Remova todas as reservas desta área para poder excluí-la">
                                            <i class="fi fi-rr-lock"></i> Excluir (possui reservas)
                                        </button>
                                    @endif
                                @endrole
                            </div>
                        @endif
                    </div>

                    <div class="ar-card__details">
                        <div class="ar-card__row">
                            <i class="fi fi-rr-calendar"></i>
                            <div class="ar-card__dias">
                                @foreach ($todosDias as $dia)
                                    <span
                                        class="ar-dia {{ in_array($dia, $area->dias_lista) ? 'ar-dia--active' : '' }}">{{ $diasAbrev[$dia] }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="ar-card__row">
                            <i class="fi fi-rr-clock"></i>
                            {{ $area->modo_reserva === 'DIA_INTEIRO' ? 'Dia inteiro' : 'Por horário' }}
                            @if ($area->capacidade_pessoas)
                                &middot; {{ $area->capacidade_pessoas }} pessoas
                            @endif
                        </div>
                        @if ($area->descricao)
                            <div class="ar-card__row">
                                <i class="fi fi-rr-info"></i>
                                {{ $area->descricao }}
                            </div>
                        @endif
                        <div class="ar-card__row">
                            <i class="fi fi-rr-bookmark"></i>
                            <span class="ar-card__reservas">
                                <i class="fi fi-rr-list"></i>
                                {{ $area->reservas_count }}
                                {{ $area->reservas_count === 1 ? 'reserva' : 'reservas' }}
                            </span>
                        </div>
                    </div>

                    <div class="ar-card__arrow">
                        <i class="fi fi-rr-angle-right"></i>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @canany(['areas.criar', 'areas.editar'])
        <div class="modal-overlay" id="modalArea">
            <div class="modal-box">
                <div class="modal-head" id="modalTitulo">Nova Área</div>
                <div class="modal-body">
                    <input type="hidden" id="area-id" value="">
                    <div>
                        <label class="sdb-label">Nome</label>
                        <input type="text" id="area-nome" class="sdb-input" placeholder="Ex: Quadra 1, Churrasqueira A">
                    </div>
                    <div class="field-row">
                        <div>
                            <label class="sdb-label">Modo de Reserva</label>
                            <select id="area-modo" class="sdb-select" onchange="toggleHorarios()">
                                <option value="HORARIO">Por horário</option>
                                <option value="DIA_INTEIRO">Dia inteiro</option>
                            </select>
                        </div>
                        <div>
                            <label class="sdb-label">Capacidade</label>
                            <input type="number" id="area-capacidade" class="sdb-input" min="1" placeholder="Ex: 30">
                        </div>
                    </div>
                    <div>
                        <label class="sdb-label">Descrição</label>
                        <textarea id="area-descricao" class="sdb-textarea" placeholder="Descrição opcional..."></textarea>
                    </div>
                    <div>
                        <label class="sdb-label">Dias disponíveis</label>
                        <div class="dias-grid" id="diasGrid"></div>
                    </div>
                    <div class="horarios-section" id="horariosSection">
                        <label class="sdb-label">Horários</label>
                        <div id="horariosContainer"></div>
                        <button type="button" class="btn-add-horario" onclick="addHorario()">
                            <i class="fi fi-rr-plus"></i> Adicionar Horário
                        </button>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                    <button class="btn-save" id="btnSalvar" onclick="salvarArea()">Salvar</button>
                </div>
            </div>
        </div>
    @endcanany

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
@endsection

@section('scripts')
    <script>
        var CSRF = '{{ csrf_token() }}';
        var TIPO_ID = {{ $tipo->id }};
        var ROUTES = {
            store: '{{ route('areas.store') }}',
            update: '{{ route('areas.update', ':id') }}',
            toggle: '{{ route('areas.toggle', ':id') }}',
            destroy: '{{ route('areas.destroy', ':id') }}',
            horarios: '{{ route('areas.horarios', ':id') }}',
            reservas: '{{ route('reservas.index') }}'
        };

        var DIAS_CONFIG = [{
                key: 'SEGUNDA',
                abbr: 'SEG'
            },
            {
                key: 'TERCA',
                abbr: 'TER'
            },
            {
                key: 'QUARTA',
                abbr: 'QUA'
            },
            {
                key: 'QUINTA',
                abbr: 'QUI'
            },
            {
                key: 'SEXTA',
                abbr: 'SEX'
            },
            {
                key: 'SABADO',
                abbr: 'SÁB'
            },
            {
                key: 'DOMINGO',
                abbr: 'DOM'
            }
        ];

        var modalState = {
            editing: false,
            id: null,
            diasSel: []
        };
        var confirmCallback = null;

        (function init() {
            var grid = document.getElementById('diasGrid');
            if (!grid) return;
            var h = '';
            for (var i = 0; i < DIAS_CONFIG.length; i++) {
                h += '<button type="button" class="dia-check" data-dia="' + DIAS_CONFIG[i].key +
                    '" onclick="toggleDia(this)">' +
                    '<div class="dia-check__box"><i class="fi fi-rr-check"></i></div>' +
                    '<span class="dia-check__abbr">' + DIAS_CONFIG[i].abbr + '</span></button>';
            }
            grid.innerHTML = h;
        })();

        function navegarArea(e, id) {
            if (e.target.closest('.ar-card__menu-btn') || e.target.closest('.ar-card__dropdown')) return;
            window.location.href = ROUTES.reservas + '?area_id=' + id;
        }

        function toggleMenu(e, id) {
            e.stopPropagation();
            var menu = document.getElementById('menu-' + id);
            var aberto = menu.classList.contains('is-open');
            fecharMenus();
            if (!aberto) menu.classList.add('is-open');
        }

        function fecharMenus() {
            document.querySelectorAll('.ar-card__dropdown.is-open').forEach(function(m) {
                m.classList.remove('is-open');
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ar-card__menu-btn') && !e.target.closest('.ar-card__dropdown')) {
                fecharMenus();
            }
        });

        function toggleDia(el) {
            el.classList.toggle('active');
            var dia = el.dataset.dia;
            var idx = modalState.diasSel.indexOf(dia);
            if (idx > -1) {
                modalState.diasSel.splice(idx, 1);
            } else {
                modalState.diasSel.push(dia);
            }
        }

        function toggleHorarios() {
            var modo = document.getElementById('area-modo').value;
            var sec = document.getElementById('horariosSection');
            if (modo === 'HORARIO') {
                sec.classList.add('is-visible');
            } else {
                sec.classList.remove('is-visible');
            }
        }

        function addHorario(valor) {
            var container = document.getElementById('horariosContainer');
            var row = document.createElement('div');
            row.className = 'horarios-row';
            row.innerHTML = '<input type="time" class="sdb-input horario-input" value="' + (valor || '') + '">' +
                '<button type="button" class="btn-rm-horario" onclick="this.parentElement.remove()">' +
                '<i class="fi fi-rr-cross-small"></i></button>';
            container.appendChild(row);
        }

        function abrirModal(data) {
            modalState.editing = !!data;
            modalState.id = data ? data.id : null;
            modalState.diasSel = data ? (data.dias_lista || []).slice() : [];

            document.getElementById('modalTitulo').textContent = data ? 'Editar Área' : 'Nova Área';
            document.getElementById('area-id').value = data ? data.id : '';
            document.getElementById('area-nome').value = data ? data.nome : '';
            document.getElementById('area-modo').value = data ? data.modo_reserva : 'HORARIO';
            document.getElementById('area-descricao').value = data ? (data.descricao || '') : '';
            document.getElementById('area-capacidade').value = data ? (data.capacidade_pessoas || '') : '';

            document.querySelectorAll('.dia-check').forEach(function(el) {
                el.classList.toggle('active', modalState.diasSel.indexOf(el.dataset.dia) > -1);
            });

            document.getElementById('horariosContainer').innerHTML = '';
            toggleHorarios();

            document.getElementById('modalArea').classList.add('is-open');

            if (data && data.modo_reserva === 'HORARIO') {
                fetch(ROUTES.horarios.replace(':id', data.id), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(info) {
                        var horariosUnicos = {};
                        var dias = Object.keys(info.horarios || {});
                        for (var i = 0; i < dias.length; i++) {
                            var lista = info.horarios[dias[i]];
                            for (var j = 0; j < lista.length; j++) {
                                horariosUnicos[lista[j]] = true;
                            }
                        }
                        var ordenados = Object.keys(horariosUnicos).sort();
                        for (var k = 0; k < ordenados.length; k++) {
                            addHorario(ordenados[k]);
                        }
                    })
                    .catch(function() {});
            }
        }

        function fecharModal() {
            document.getElementById('modalArea').classList.remove('is-open');
        }

        function editarArea(e, data) {
            e.stopPropagation();
            fecharMenus();
            abrirModal(data);
        }

        function salvarArea() {
            var nome = document.getElementById('area-nome').value.trim();
            if (!nome) {
                SdbToast.error('Informe o nome da área.');
                return;
            }
            if (modalState.diasSel.length === 0) {
                SdbToast.error('Selecione ao menos um dia.');
                return;
            }

            var horarios = [];
            document.querySelectorAll('.horario-input').forEach(function(el) {
                if (el.value) horarios.push(el.value);
            });

            var modo = document.getElementById('area-modo').value;
            if (modo === 'HORARIO' && horarios.length === 0 && !modalState.editing) {
                SdbToast.error('Adicione ao menos um horário.');
                return;
            }

            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

            var payload = {
                nome: nome,
                tipo_area_id: TIPO_ID,
                modo_reserva: modo,
                descricao: document.getElementById('area-descricao').value.trim() || null,
                capacidade_pessoas: parseInt(document.getElementById('area-capacidade').value) || null,
                dias: modalState.diasSel
            };

            if (horarios.length > 0) payload.horarios = horarios;

            var url = modalState.editing ? ROUTES.update.replace(':id', modalState.id) : ROUTES.store;
            var method = modalState.editing ? 'PUT' : 'POST';

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(function(r) {
                    if (!r.ok) return r.json().then(function(e) {
                        throw e;
                    });
                    return r.json();
                })
                .then(function() {
                    fecharModal();
                    SdbToast.success(modalState.editing ? 'Área atualizada.' : 'Área criada.');
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    var msg = err.message || 'Erro ao salvar área.';
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
            document.getElementById('confirmTitulo').textContent = 'Desativar Área';
            document.getElementById('confirmMsg').textContent = 'A área "' + nome +
                '" será desativada e não aceitará novas reservas. As reservas existentes serão mantidas.';
            var btn = document.getElementById('confirmBtn');
            btn.className = 'btn-warning';
            btn.innerHTML = '<i class="fi fi-rr-ban"></i> Desativar';
            confirmCallback = function() {
                executarToggle(id);
            };
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function ativarArea(e, id) {
            e.stopPropagation();
            fecharMenus();
            executarToggle(id);
        }

        function executarToggle(id) {
            fetch(ROUTES.toggle.replace(':id', id), {
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

        function confirmarExcluir(e, id, nome) {
            e.stopPropagation();
            fecharMenus();
            var ic = document.getElementById('confirmIc');
            ic.className = 'confirm-box__ic confirm-box__ic--danger';
            ic.innerHTML = '<i class="fi fi-rr-trash"></i>';
            document.getElementById('confirmTitulo').textContent = 'Excluir Área';
            document.getElementById('confirmMsg').textContent = 'A área "' + nome +
                '" será excluída permanentemente junto com seus horários, dias e valores configurados. Esta ação não pode ser desfeita.';
            var btn = document.getElementById('confirmBtn');
            btn.className = 'btn-danger';
            btn.innerHTML = '<i class="fi fi-rr-trash"></i> Excluir';
            confirmCallback = function() {
                executarExcluir(id);
            };
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function executarExcluir(id) {
            fetch(ROUTES.destroy.replace(':id', id), {
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
