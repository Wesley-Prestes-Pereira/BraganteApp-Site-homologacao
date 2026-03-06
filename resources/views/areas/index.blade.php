@extends('layouts.app')

@section('title', 'Áreas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Áreas</h1>
            <p class="page-header__sub">{{ $totalAreas }} {{ $totalAreas === 1 ? 'área cadastrada' : 'áreas cadastradas' }}
            </p>
        </div>
        @can('areas.criar')
            <button class="btn-primary" onclick="abrirModalArea()">
                <i class="fi fi-rr-plus-small"></i> Nova Área
            </button>
        @endcan
    </div>
@endsection

@section('styles')
    <style>
        .a-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .a-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: background .3s, border-color .3s;
        }

        .a-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .a-stat__val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
        }

        .a-stat__lbl {
            font-size: .78rem;
            font-weight: 500;
            color: var(--t3);
            margin-top: 2px;
        }

        .a-section {
            margin-bottom: 24px;
        }

        .a-section__title {
            font-size: .92rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .a-section__title i {
            font-size: .88rem;
        }

        .a-section__count {
            background: var(--hover);
            color: var(--t3);
            font-size: .72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .a-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 12px;
        }

        .a-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: all .2s ease;
        }

        .a-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
        }

        .a-card--inactive {
            opacity: .55;
        }

        .a-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .a-card__info {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .a-card__name {
            font-size: .95rem;
            font-weight: 700;
            color: var(--t1);
        }

        .a-card__tipo {
            font-size: .72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            width: fit-content;
        }

        .a-card__tipo i {
            font-size: .62rem;
        }

        .a-card__status {
            font-size: .72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .a-card__status--ativa {
            background: rgba(52, 211, 153, .12);
            color: var(--success);
        }

        .a-card__status--inativa {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        [data-theme="light"] .a-card__status--ativa {
            background: rgba(16, 185, 129, .08);
        }

        [data-theme="light"] .a-card__status--inativa {
            background: rgba(239, 68, 68, .08);
        }

        .a-card__status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .a-card__details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .a-card__row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            color: var(--t3);
        }

        .a-card__row i {
            font-size: .78rem;
            color: var(--t4);
            width: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        .a-card__dias {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
        }

        .a-dia {
            font-size: .65rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 6px;
            background: var(--hover);
            color: var(--t4);
            transition: all .15s;
        }

        .a-dia--active {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        [data-theme="light"] .a-dia--active {
            background: rgba(59, 130, 246, .10);
        }

        .a-card__reservas {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: .72rem;
            font-weight: 600;
            background: rgba(167, 139, 250, .12);
            color: var(--purple, #a78bfa);
        }

        [data-theme="light"] .a-card__reservas {
            background: rgba(139, 92, 246, .08);
        }

        .a-card__reservas i {
            font-size: .62rem;
        }

        .a-card__footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            padding-top: 12px;
            border-top: 1px solid var(--card-border);
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

        .btn-ic {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
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
            border-color: var(--input-border-h);
        }

        .btn-ic.success:hover {
            color: var(--success);
            background: rgba(52, 211, 153, .08);
            border-color: rgba(52, 211, 153, .25);
        }

        .btn-ic.warning:hover {
            color: var(--warning);
            background: rgba(251, 191, 36, .08);
            border-color: rgba(251, 191, 36, .25);
        }

        .btn-ic.danger:hover {
            color: var(--danger);
            background: rgba(248, 113, 113, .08);
            border-color: rgba(248, 113, 113, .25);
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

        .empty-state__text {
            font-size: .9rem;
            color: var(--t3);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: var(--overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all .25s ease;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            width: 100%;
            max-width: 600px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .3);
            transform: translateY(12px) scale(.98);
            transition: all .25s ease;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.is-open .modal-box {
            transform: translateY(0) scale(1);
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--card-border);
            flex-shrink: 0;
        }

        .modal-head__title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
        }

        .modal-head__close {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: none;
            background: none;
            color: var(--t4);
            cursor: pointer;
            font-size: .9rem;
            transition: all .15s ease;
        }

        .modal-head__close:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .modal-body {
            padding: 22px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            padding: 14px 22px;
            border-top: 1px solid var(--card-border);
            flex-shrink: 0;
        }

        .field {
            margin-bottom: 16px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t2);
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
            background: var(--bg);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            color: var(--t1);
            transition: border-color .15s;
        }

        .sdb-input:focus,
        .sdb-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .tipo-select-row {
            display: flex;
            gap: 8px;
        }

        .tipo-select-row .sdb-select {
            flex: 1;
        }

        .btn-novo-tipo {
            height: 42px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: transparent;
            border: 1px dashed var(--card-border);
            border-radius: 10px;
            color: var(--accent);
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s;
        }

        .btn-novo-tipo:hover {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .06);
        }

        .novo-tipo-panel {
            display: none;
            margin-top: 12px;
            padding: 16px;
            background: var(--bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
        }

        .novo-tipo-panel.is-open {
            display: block;
        }

        .novo-tipo-panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .novo-tipo-panel__title {
            font-size: .82rem;
            font-weight: 700;
            color: var(--t1);
        }

        .novo-tipo-panel__close {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: none;
            background: none;
            color: var(--t4);
            cursor: pointer;
            font-size: .78rem;
        }

        .novo-tipo-panel__close:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 6px;
            max-height: 180px;
            overflow-y: auto;
            padding: 4px;
        }

        .icon-grid::-webkit-scrollbar {
            width: 4px;
        }

        .icon-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .icon-grid::-webkit-scrollbar-thumb {
            background: var(--card-border);
            border-radius: 4px;
        }

        .icon-opt {
            width: 100%;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--card-border);
            border-radius: 10px;
            background: var(--card);
            color: var(--t3);
            cursor: pointer;
            font-size: .92rem;
            transition: all .15s;
        }

        .icon-opt:hover {
            border-color: var(--t4);
            color: var(--t1);
            background: var(--hover);
        }

        .icon-opt.active {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(91, 156, 246, .08);
        }

        .color-grid {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding: 4px;
        }

        .color-opt {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all .15s;
            position: relative;
        }

        .color-opt:hover {
            transform: scale(1.15);
        }

        .color-opt.active {
            border-color: var(--t1);
            box-shadow: 0 0 0 2px var(--card);
        }

        .color-opt.active::after {
            content: '';
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .5);
        }

        .novo-tipo-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-sm {
            height: 32px;
            padding: 0 14px;
            border-radius: 8px;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            border: none;
        }

        .btn-sm--primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-sm--primary:hover {
            background: var(--accent-h);
        }

        .btn-sm--primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-sm--ghost {
            background: transparent;
            color: var(--t3);
            border: 1px solid var(--card-border);
        }

        .btn-sm--ghost:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .preview-tipo {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 10px;
            margin-top: 8px;
            font-size: .82rem;
            font-weight: 600;
        }

        .preview-tipo.is-visible {
            display: inline-flex;
        }

        .preview-tipo i {
            font-size: .88rem;
        }

        .dias-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .dia-check {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 4px;
            border: 2px solid var(--card-border);
            border-radius: 10px;
            cursor: pointer;
            transition: all .2s ease;
            user-select: none;
        }

        .dia-check:hover {
            border-color: var(--input-border-h);
            background: var(--hover);
        }

        .dia-check.active {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .06);
        }

        [data-theme="light"] .dia-check.active {
            background: rgba(59, 130, 246, .04);
        }

        .dia-check__abbr {
            font-size: .68rem;
            font-weight: 700;
            color: var(--t3);
            transition: color .2s;
        }

        .dia-check.active .dia-check__abbr {
            color: var(--accent);
        }

        .dia-check__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--card-border);
            transition: background .2s;
        }

        .dia-check.active .dia-check__dot {
            background: var(--accent);
        }

        .field-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .btn-cancel {
            height: 38px;
            padding: 0 18px;
            background: transparent;
            color: var(--t3);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-cancel:hover {
            color: var(--t1);
            background: var(--hover);
            border-color: var(--input-border-h);
        }

        .btn-save {
            height: 38px;
            padding: 0 22px;
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
            z-index: 3000;
            background: var(--overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all .25s ease;
        }

        .confirm-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .confirm-box {
            width: 100%;
            max-width: 380px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .3);
            text-align: center;
        }

        .confirm-box__ic {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .confirm-box__ic--danger {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        .confirm-box__title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 6px;
        }

        .confirm-box__msg {
            font-size: .84rem;
            color: var(--t3);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .confirm-box__actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-danger {
            height: 38px;
            padding: 0 22px;
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .15s;
        }

        .btn-danger:hover {
            opacity: .85;
        }

        @media (max-width: 768px) {
            .a-stats {
                grid-template-columns: repeat(3, 1fr);
            }

            .a-grid {
                grid-template-columns: 1fr;
            }

            .dias-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .field-row-3 {
                grid-template-columns: 1fr;
            }

            .icon-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        @media (max-width: 425px) {
            .a-stats {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .a-stat {
                padding: 14px 16px;
                gap: 10px;
                border-radius: 12px;
            }

            .a-stat__ic {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                font-size: .88rem;
            }

            .a-stat__val {
                font-size: 1.2rem;
            }

            .a-stat__lbl {
                font-size: .72rem;
            }

            .a-card {
                padding: 16px 18px;
                gap: 12px;
                border-radius: 12px;
            }

            .a-card__name {
                font-size: .9rem;
            }

            .btn-ic {
                width: 32px;
                height: 32px;
                font-size: .8rem;
                border-radius: 7px;
            }

            .modal-box {
                border-radius: 16px;
                max-width: 100%;
            }

            .modal-head {
                padding: 14px 18px;
            }

            .modal-body {
                padding: 18px;
            }

            .modal-foot {
                padding: 12px 18px;
            }

            .dias-grid {
                gap: 4px;
            }

            .dia-check {
                padding: 8px 2px;
                border-radius: 8px;
            }

            .dia-check__abbr {
                font-size: .6rem;
            }

            .icon-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .tipo-select-row {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    <div class="a-stats">
        <div class="a-stat">
            <div class="a-stat__ic" style="background:rgba(91,156,246,.15);color:var(--accent)"><i
                    class="fi fi-rr-marker"></i></div>
            <div>
                <div class="a-stat__val">{{ $totalAreas }}</div>
                <div class="a-stat__lbl">Total</div>
            </div>
        </div>
        <div class="a-stat">
            <div class="a-stat__ic" style="background:rgba(52,211,153,.15);color:var(--success)"><i
                    class="fi fi-rr-check-circle"></i></div>
            <div>
                <div class="a-stat__val">{{ $totalAreas - $inativas }}</div>
                <div class="a-stat__lbl">Ativas</div>
            </div>
        </div>
        <div class="a-stat">
            <div class="a-stat__ic" style="background:rgba(248,113,113,.15);color:var(--danger)"><i
                    class="fi fi-rr-ban"></i></div>
            <div>
                <div class="a-stat__val">{{ $inativas }}</div>
                <div class="a-stat__lbl">Inativas</div>
            </div>
        </div>
    </div>

    @if ($areas->isEmpty())
        <div class="empty-state">
            <i class="fi fi-rr-marker"></i>
            <p class="empty-state__text">Nenhuma área cadastrada</p>
        </div>
    @else
        @foreach ($grupos as $grupo)
            <div class="a-section">
                <div class="a-section__title">
                    <i class="fi {{ $grupo->tipo->icone }}" style="color:{{ $grupo->tipo->cor }}"></i>
                    {{ $grupo->tipo->nome }}
                    <span class="a-section__count">{{ $grupo->areas->count() }}</span>
                </div>
                <div class="a-grid">
                    @foreach ($grupo->areas as $area)
                        <div class="a-card {{ $area->ativo ? '' : 'a-card--inactive' }}">
                            <div class="a-card__top">
                                <div class="a-card__info">
                                    <div class="a-card__name">{{ $area->nome }}</div>
                                    <div class="a-card__tipo"
                                        style="background:{{ $area->cor }}18;color:{{ $area->cor }}">
                                        <i class="fi {{ $area->icone }}"></i>
                                        {{ $area->tipo_nome }}
                                    </div>
                                </div>
                                <div class="a-card__status a-card__status--{{ $area->ativo ? 'ativa' : 'inativa' }}">
                                    <span class="a-card__status-dot"></span>
                                    {{ $area->ativo ? 'Ativa' : 'Inativa' }}
                                </div>
                            </div>

                            <div class="a-card__details">
                                <div class="a-card__row">
                                    <i class="fi fi-rr-calendar"></i>
                                    <div class="a-card__dias">
                                        @foreach ($todosDias as $dia)
                                            <span
                                                class="a-dia {{ in_array($dia, $area->dias_lista) ? 'a-dia--active' : '' }}">{{ $diasAbrev[$dia] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="a-card__row">
                                    <i class="fi fi-rr-clock"></i>
                                    {{ $area->modo_reserva === 'DIA_INTEIRO' ? 'Dia inteiro' : 'Por horário' }}
                                    @if ($area->descricao)
                                        &middot; {{ $area->descricao }}
                                    @endif
                                </div>
                                <div class="a-card__row">
                                    <i class="fi fi-rr-bookmark"></i>
                                    <span class="a-card__reservas">
                                        <i class="fi fi-rr-list"></i>
                                        {{ $area->reservas_count }}
                                        {{ $area->reservas_count === 1 ? 'reserva' : 'reservas' }}
                                    </span>
                                </div>
                            </div>

                            <div class="a-card__footer">
                                @can('areas.editar')
                                    <button class="btn-ic {{ $area->ativo ? 'warning' : 'success' }}"
                                        onclick="toggleStatus({{ $area->id }})"
                                        title="{{ $area->ativo ? 'Desativar' : 'Ativar' }}">
                                        <i class="fi fi-rr-{{ $area->ativo ? 'eye-crossed' : 'eye' }}"></i>
                                    </button>
                                    <button class="btn-ic"
                                        onclick="abrirModalArea({{ Js::from($area->only('id', 'nome', 'tipo_area_id', 'modo_reserva', 'descricao', 'capacidade_pessoas')) }}, {{ Js::from($area->dias_lista) }})"
                                        title="Editar">
                                        <i class="fi fi-rr-pencil"></i>
                                    </button>
                                @endcan
                                @role('admin')
                                    @if ($area->reservas_count === 0)
                                        <button class="btn-ic danger" onclick="excluirArea({{ $area->id }})"
                                            title="Excluir">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    @endif
                                @endrole
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    <div class="modal-overlay" id="modalArea">
        <div class="modal-box">
            <div class="modal-head">
                <span class="modal-head__title" id="modalAreaTitulo">Nova Área</span>
                <button class="modal-head__close" onclick="fecharModal()"><i class="fi fi-rr-cross-small"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="area-id">

                <div class="field">
                    <label class="sdb-label">Nome <span class="req">*</span></label>
                    <input type="text" id="area-nome" class="sdb-input" placeholder="Ex: Quadra 1">
                </div>

                <div class="field">
                    <label class="sdb-label">Tipo de Área <span class="req">*</span></label>
                    <div class="tipo-select-row">
                        <select id="area-tipo" class="sdb-select" onchange="onTipoChange()">
                            <option value="">Selecione o tipo...</option>
                            @foreach ($tiposArea as $t)
                                <option value="{{ $t->id }}" data-icone="{{ $t->icone }}"
                                    data-cor="{{ $t->cor }}">{{ $t->nome }}</option>
                            @endforeach
                        </select>
                        @role('admin')
                            <button type="button" class="btn-novo-tipo" onclick="toggleNovoTipo()">
                                <i class="fi fi-rr-plus-small"></i> Novo
                            </button>
                        @endrole
                    </div>
                    <div class="preview-tipo" id="previewTipo">
                        <i id="previewTipoIcon"></i>
                        <span id="previewTipoNome"></span>
                    </div>

                    <div class="novo-tipo-panel" id="novoTipoPanel">
                        <div class="novo-tipo-panel__head">
                            <span class="novo-tipo-panel__title">Criar novo tipo</span>
                            <button type="button" class="novo-tipo-panel__close" onclick="toggleNovoTipo()"><i
                                    class="fi fi-rr-cross-small"></i></button>
                        </div>

                        <div class="field">
                            <label class="sdb-label">Nome do tipo <span class="req">*</span></label>
                            <input type="text" id="novo-tipo-nome" class="sdb-input" placeholder="Ex: Piscina">
                        </div>

                        <div class="field">
                            <label class="sdb-label">Ícone <span class="req">*</span></label>
                            <div class="icon-grid" id="iconGrid"></div>
                        </div>

                        <div class="field">
                            <label class="sdb-label">Cor <span class="req">*</span></label>
                            <div class="color-grid" id="colorGrid"></div>
                        </div>

                        <div class="novo-tipo-actions">
                            <button type="button" class="btn-sm btn-sm--ghost"
                                onclick="toggleNovoTipo()">Cancelar</button>
                            <button type="button" class="btn-sm btn-sm--primary" id="btnSalvarTipo"
                                onclick="salvarNovoTipo()">Criar tipo</button>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="sdb-label">Modo de Reserva <span class="req">*</span></label>
                    <select id="area-modo" class="sdb-select">
                        <option value="HORARIO">Por Horário</option>
                        <option value="DIA_INTEIRO">Dia Inteiro</option>
                    </select>
                </div>

                <div class="field">
                    <label class="sdb-label">Dias Disponíveis <span class="req">*</span></label>
                    <div class="dias-grid">
                        <div class="dia-check" data-dia="SEGUNDA" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">SEG</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="TERCA" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">TER</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="QUARTA" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">QUA</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="QUINTA" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">QUI</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="SEXTA" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">SEX</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="SABADO" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">SÁB</span><span class="dia-check__dot"></span></div>
                        <div class="dia-check" data-dia="DOMINGO" onclick="toggleDia(this)"><span
                                class="dia-check__abbr">DOM</span><span class="dia-check__dot"></span></div>
                    </div>
                </div>

                <div class="field">
                    <label class="sdb-label">Descrição</label>
                    <input type="text" id="area-descricao" class="sdb-input"
                        placeholder="Ex: Grama sintética, com iluminação">
                </div>

                <div class="field">
                    <label class="sdb-label">Capacidade (pessoas)</label>
                    <input type="number" id="area-capacidade" class="sdb-input" min="1" placeholder="Ex: 30">
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button class="btn-save" id="btnSalvar" onclick="salvarArea()">Salvar</button>
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
                <button class="btn-danger" id="confirmBtn" onclick="executarConfirm()"></button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var CSRF = '{{ csrf_token() }}';
        var ROUTES = {
            store: '{{ route('areas.store') }}',
            update: '{{ route('areas.update', ':id') }}',
            toggle: '{{ route('areas.toggle', ':id') }}',
            destroy: '{{ route('areas.destroy', ':id') }}',
            tipoStore: '{{ route('tipos-area.store') }}'
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
                id: 'fi-rr-star',
                label: 'Estrela'
            },
            {
                id: 'fi-rr-heart',
                label: 'Coração'
            },
            {
                id: 'fi-rr-shield',
                label: 'Escudo'
            },
            {
                id: 'fi-rr-target',
                label: 'Alvo'
            },
            {
                id: 'fi-rr-bolt',
                label: 'Raio'
            },
            {
                id: 'fi-rr-sun',
                label: 'Sol'
            },
            {
                id: 'fi-rr-music-alt',
                label: 'Música'
            },
            {
                id: 'fi-rr-gamepad',
                label: 'Game'
            },
            {
                id: 'fi-rr-users',
                label: 'Grupo'
            },
            {
                id: 'fi-rr-ticket',
                label: 'Ticket'
            },
            {
                id: 'fi-rr-diamond',
                label: 'Diamante'
            },
            {
                id: 'fi-rr-crown',
                label: 'Coroa'
            },
            {
                id: 'fi-rr-rocket',
                label: 'Foguete'
            },
            {
                id: 'fi-rr-puzzle',
                label: 'Puzzle'
            },
            {
                id: 'fi-rr-leaf',
                label: 'Folha'
            },
            {
                id: 'fi-rr-globe',
                label: 'Globo'
            },
            {
                id: 'fi-rr-compass',
                label: 'Bússola'
            },
            {
                id: 'fi-rr-palette',
                label: 'Paleta'
            },
            {
                id: 'fi-rr-running',
                label: 'Corrida'
            },
            {
                id: 'fi-rr-swimming-pool',
                label: 'Piscina'
            },
            {
                id: 'fi-rr-gym',
                label: 'Academia'
            }
        ];

        var CORES = [
            '#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6',
            '#ec4899', '#14b8a6', '#6366f1', '#06b6d4', '#84cc16',
            '#f43f5e', '#0ea5e9', '#d946ef', '#22c55e', '#e11d48',
            '#7c3aed'
        ];

        var modalState = {
            editing: false,
            id: null
        };
        var confirmCallback = null;
        var novoTipoState = {
            icone: null,
            cor: null
        };

        (function initGrids() {
            var iconGrid = document.getElementById('iconGrid');
            var h = '';
            for (var i = 0; i < ICONES.length; i++) {
                h += '<button type="button" class="icon-opt" data-icon="' + ICONES[i].id + '" title="' + ICONES[i]
                    .label + '" onclick="selecionarIcone(this)">' +
                    '<i class="fi ' + ICONES[i].id + '"></i></button>';
            }
            iconGrid.innerHTML = h;

            var colorGrid = document.getElementById('colorGrid');
            var c = '';
            for (var j = 0; j < CORES.length; j++) {
                c += '<button type="button" class="color-opt" data-cor="' + CORES[j] + '" style="background:' + CORES[
                    j] + '" title="' + CORES[j] + '" onclick="selecionarCor(this)"></button>';
            }
            colorGrid.innerHTML = c;
        })();

        function selecionarIcone(el) {
            document.querySelectorAll('.icon-opt').forEach(function(o) {
                o.classList.remove('active');
            });
            el.classList.add('active');
            novoTipoState.icone = el.dataset.icon;
        }

        function selecionarCor(el) {
            document.querySelectorAll('.color-opt').forEach(function(o) {
                o.classList.remove('active');
            });
            el.classList.add('active');
            novoTipoState.cor = el.dataset.cor;
        }

        function toggleNovoTipo() {
            var panel = document.getElementById('novoTipoPanel');
            panel.classList.toggle('is-open');
            if (!panel.classList.contains('is-open')) {
                document.getElementById('novo-tipo-nome').value = '';
                document.querySelectorAll('.icon-opt').forEach(function(o) {
                    o.classList.remove('active');
                });
                document.querySelectorAll('.color-opt').forEach(function(o) {
                    o.classList.remove('active');
                });
                novoTipoState = {
                    icone: null,
                    cor: null
                };
            }
        }

        function salvarNovoTipo() {
            var nome = document.getElementById('novo-tipo-nome').value.trim().toUpperCase();
            if (!nome) {
                SdbToast.error('Informe o nome do tipo.');
                return;
            }
            if (!novoTipoState.icone) {
                SdbToast.error('Selecione um ícone.');
                return;
            }
            if (!novoTipoState.cor) {
                SdbToast.error('Selecione uma cor.');
                return;
            }

            var btn = document.getElementById('btnSalvarTipo');
            btn.disabled = true;

            fetch(ROUTES.tipoStore, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nome: nome,
                        icone: novoTipoState.icone,
                        cor: novoTipoState.cor
                    })
                })
                .then(function(r) {
                    if (!r.ok) return r.json().then(function(e) {
                        throw e;
                    });
                    return r.json();
                })
                .then(function(tipo) {
                    var select = document.getElementById('area-tipo');
                    var opt = document.createElement('option');
                    opt.value = tipo.id;
                    opt.textContent = tipo.nome;
                    opt.dataset.icone = tipo.icone;
                    opt.dataset.cor = tipo.cor;
                    select.insertBefore(opt, select.lastElementChild || null);
                    select.value = tipo.id;
                    onTipoChange();
                    toggleNovoTipo();
                    SdbToast.success('Tipo "' + tipo.nome + '" criado.');
                    btn.disabled = false;
                })
                .catch(function(err) {
                    btn.disabled = false;
                    var msg = err.message || 'Erro ao criar tipo.';
                    if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                    SdbToast.error(msg);
                });
        }

        function onTipoChange() {
            var select = document.getElementById('area-tipo');
            var opt = select.options[select.selectedIndex];
            var preview = document.getElementById('previewTipo');
            if (select.value && opt) {
                var icone = opt.dataset.icone || 'fi-rr-marker';
                var cor = opt.dataset.cor || '#3b82f6';
                document.getElementById('previewTipoIcon').className = 'fi ' + icone;
                document.getElementById('previewTipoIcon').style.color = cor;
                document.getElementById('previewTipoNome').textContent = opt.textContent;
                preview.style.background = cor + '15';
                preview.style.color = cor;
                preview.classList.add('is-visible');
            } else {
                preview.classList.remove('is-visible');
            }
        }

        function abrirModalArea(data, dias) {
            modalState.editing = !!data;
            modalState.id = data ? data.id : null;

            document.getElementById('modalAreaTitulo').textContent = data ? 'Editar Área' : 'Nova Área';
            document.getElementById('area-id').value = data ? data.id : '';
            document.getElementById('area-nome').value = data ? data.nome : '';
            document.getElementById('area-modo').value = data ? data.modo_reserva : 'HORARIO';
            document.getElementById('area-descricao').value = data ? (data.descricao || '') : '';
            document.getElementById('area-capacidade').value = data ? (data.capacidade_pessoas || '') : '';
            document.getElementById('area-tipo').value = data ? data.tipo_area_id : '';

            onTipoChange();

            var panel = document.getElementById('novoTipoPanel');
            if (panel.classList.contains('is-open')) toggleNovoTipo();

            document.querySelectorAll('.dia-check').forEach(function(d) {
                d.classList.remove('active');
            });
            if (dias && dias.length) {
                dias.forEach(function(dia) {
                    var el = document.querySelector('.dia-check[data-dia="' + dia + '"]');
                    if (el) el.classList.add('active');
                });
            }

            document.getElementById('modalArea').classList.add('is-open');
        }

        function fecharModal() {
            document.getElementById('modalArea').classList.remove('is-open');
        }

        function toggleDia(el) {
            el.classList.toggle('active');
        }

        function getSelectedDias() {
            return Array.from(document.querySelectorAll('.dia-check.active')).map(function(d) {
                return d.dataset.dia;
            });
        }

        function salvarArea() {
            var nome = document.getElementById('area-nome').value.trim();
            var tipoAreaId = document.getElementById('area-tipo').value;

            var dias = [];
            document.querySelectorAll('.dia-check.active').forEach(function(d) {
                dias.push(d.dataset.dia);
            });

            if (!nome) {
                SdbToast.error('Informe o nome da área');
                return;
            }
            if (!tipoAreaId) {
                SdbToast.error('Selecione o tipo de área');
                return;
            }
            if (dias.length === 0) {
                SdbToast.error('Selecione ao menos um dia');
                return;
            }

            var payload = {
                nome: nome,
                tipo_area_id: parseInt(tipoAreaId),
                modo_reserva: document.getElementById('area-modo').value,
                descricao: document.getElementById('area-descricao').value.trim() || null,
                capacidade_pessoas: parseInt(document.getElementById('area-capacidade').value) || null,
                dias: dias
            };

            var btn = document.getElementById('btnSalvar');
            btn.disabled = true;

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
                    SdbToast.success(modalState.editing ? 'Área atualizada' : 'Área criada');
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    var msg = err.message || 'Erro ao salvar área.';
                    if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                    SdbToast.error(msg);
                });
        }

        function toggleStatus(id) {
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
                    SdbToast.success(data.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 600);
                })
                .catch(function(err) {
                    SdbToast.error(err.message || 'Erro ao alterar status.');
                });
        }

        function excluirArea(id) {
            confirmCallback = function() {
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
                    .then(function() {
                        SdbToast.success('Área excluída');
                        setTimeout(function() {
                            window.location.reload();
                        }, 800);
                    })
                    .catch(function(err) {
                        SdbToast.error(err.message || 'Erro ao excluir área.');
                    });
            };

            document.getElementById('confirmIc').innerHTML =
                '<i class="fi fi-rr-trash" style="color:var(--danger);font-size:1.3rem"></i>';
            document.getElementById('confirmTitulo').textContent = 'Excluir área';
            document.getElementById('confirmMsg').textContent = 'Esta ação é permanente e não pode ser desfeita.';
            document.getElementById('confirmBtn').textContent = 'Excluir';
            document.getElementById('confirmOverlay').classList.add('is-open');
        }

        function fecharConfirm() {
            document.getElementById('confirmOverlay').classList.remove('is-open');
            confirmCallback = null;
        }

        function executarConfirm() {
            if (confirmCallback) confirmCallback();
        }

        document.getElementById('modalArea').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });

        document.getElementById('confirmOverlay').addEventListener('click', function(e) {
            if (e.target === this) fecharConfirm();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('confirmOverlay').classList.contains('is-open')) {
                    fecharConfirm();
                } else if (document.getElementById('modalArea').classList.contains('is-open')) {
                    fecharModal();
                }
            }
        });
    </script>
@endsection
