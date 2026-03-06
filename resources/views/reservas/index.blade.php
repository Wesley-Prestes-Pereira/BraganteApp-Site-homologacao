@extends('layouts.app')

@section('title', 'Reservas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title" id="pageTitle">Reservas</h1>
            <p class="page-header__sub" id="pageSub"></p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <button class="act-btn" onclick="window.print()" title="Imprimir"><i class="fi fi-rr-print"></i></button>
            <a href="#" class="act-btn" id="btnExportPdf" target="_blank" title="Exportar PDF"><i
                    class="fi fi-rr-document"></i></a>
            <a href="#" class="act-btn" id="btnExportXlsx" target="_blank" title="Exportar Excel"><i
                    class="fi fi-rr-download"></i></a>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        .rv-panel,
        .rv-lane {
            contain: layout style;
        }

        .rv-chip,
        .rv-livre,
        .rv-tab,
        .rv-day,
        .rv-tipo-chip,
        .rv-nav-btn,
        .rv-today-btn,
        .rv-view-btn,
        .btn-primary,
        .btn-ghost,
        .btn-danger,
        .act-btn {
            touch-action: manipulation;
        }

        .rv-toolbar {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
            transition: background .35s ease, border-color .35s ease;
        }

        .rv-toolbar__row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .rv-toolbar__row+.rv-toolbar__row {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--card-border);
        }

        .rv-toolbar__sep {
            width: 1px;
            height: 24px;
            background: var(--card-border);
            flex-shrink: 0;
        }

        .rv-tabs {
            display: inline-flex;
            gap: 2px;
            background: var(--bg);
            border-radius: 10px;
            padding: 3px;
        }

        .rv-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border: none;
            background: transparent;
            color: var(--t3);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s;
        }

        .rv-tab:hover {
            color: var(--t1);
        }

        .rv-tab.active {
            background: var(--card);
            color: var(--t1);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        }

        [data-theme="dark"] .rv-tab.active {
            background: var(--accent);
            color: #fff;
        }

        .rv-tipo-chips {
            display: inline-flex;
            gap: 3px;
        }

        .rv-tipo-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 34px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .rv-tipo-chip:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .rv-tipo-chip.active {
            color: var(--accent);
            border-color: var(--accent);
            background: rgba(91, 156, 246, .08);
        }

        [data-theme="light"] .rv-tipo-chip.active {
            background: rgba(59, 130, 246, .06);
        }

        .rv-tipo-chip i {
            font-size: .82rem;
        }

        .rv-search {
            height: 34px;
            flex: 1;
            min-width: 200px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: var(--bg);
            color: var(--t1);
            font-family: inherit;
            font-size: .82rem;
            outline: none;
            transition: border-color .15s;
        }

        .rv-search:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--input-glow, rgba(91, 156, 246, .12));
        }

        [data-theme="dark"] .rv-search {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .10);
        }

        .rv-nav-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t2);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            transition: all .15s;
        }

        .rv-nav-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .rv-week-label {
            font-size: .86rem;
            font-weight: 700;
            color: var(--t1);
            min-width: 180px;
            text-align: center;
        }

        .rv-today-btn {
            padding: 4px 12px;
            border-radius: 7px;
            border: 1px solid var(--accent);
            background: transparent;
            color: var(--accent);
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }

        .rv-today-btn:hover {
            background: var(--accent);
            color: #fff;
        }

        .rv-days {
            display: flex;
            gap: 6px;
            margin-bottom: 14px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .rv-day {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            padding: 8px 14px;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: var(--card);
            cursor: pointer;
            transition: all .15s;
            min-width: 56px;
            position: relative;
        }

        .rv-day:hover {
            border-color: var(--accent);
        }

        .rv-day.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(91, 156, 246, .3);
        }

        .rv-day.today:not(.active) {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .06);
        }

        .rv-day__abbr {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .rv-day__num {
            font-size: 1rem;
            font-weight: 600;
            color: var(--t1);
        }

        .rv-day.active .rv-day__num {
            color: #fff;
            opacity: .9;
        }

        .rv-day__count {
            font-size: .58rem;
            font-weight: 700;
            background: var(--bg);
            color: var(--t3);
            border-radius: 4px;
            padding: 1px 5px;
        }

        .rv-day.active .rv-day__count {
            background: rgba(255, 255, 255, .25);
            color: #fff;
        }

        .rv-stats {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }

        .rv-stat {
            flex: 1;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rv-stat__num {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--t1);
        }

        .rv-stat__label {
            font-size: .76rem;
            color: var(--t3);
            font-weight: 500;
        }

        .rv-stat--green {
            border-left: 3px solid var(--success, #22c55e);
        }

        .rv-stat--blue {
            border-left: 3px solid var(--accent);
        }

        .rv-stat--amber {
            border-left: 3px solid var(--warning, #f59e0b);
        }

        .rv-lanes {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            align-items: flex-start;
            padding-bottom: 4px;
        }

        .rv-lane {
            flex: 1;
            min-width: 180px;
            max-width: 340px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        .rv-lane__head {
            padding: 12px 14px;
            border-bottom: 2px solid var(--card-border);
            background: var(--bg);
        }

        .rv-lane__name {
            font-weight: 700;
            font-size: .88rem;
            color: var(--t1);
        }

        .rv-lane__desc {
            font-size: .7rem;
            color: var(--t4, #94a3b8);
            margin-top: 1px;
        }

        .rv-lane__body {
            max-height: 520px;
            overflow-y: auto;
        }

        .rv-lane__foot {
            padding: 8px 14px;
            border-top: 1px solid var(--card-border);
            font-size: .72rem;
            color: var(--t4, #94a3b8);
            font-weight: 600;
            text-align: center;
            background: var(--bg);
        }

        .rv-slot {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-bottom: 1px solid var(--card-border);
            min-height: 40px;
        }

        .rv-slot:last-child {
            border-bottom: none;
        }

        .rv-slot:hover {
            background: var(--hover, rgba(0, 0, 0, .02));
        }

        .rv-slot__time {
            font-size: .78rem;
            font-weight: 600;
            color: var(--t2);
            min-width: 42px;
            flex-shrink: 0;
        }

        .rv-slot__body {
            flex: 1;
            min-width: 0;
        }

        .rv-slot--today {
            background: rgba(91, 156, 246, .04);
        }

        .rv-slot--today .rv-slot__time {
            color: var(--accent);
            font-weight: 700;
        }

        .rv-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            width: 100%;
            padding: 5px 8px;
            border-radius: 8px;
            border: 1px solid;
            cursor: pointer;
            font-family: inherit;
            font-size: .76rem;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            transition: all .12s;
            background: var(--rv-fixa-bg);
            color: var(--rv-fixa-text);
            border-color: rgba(91, 156, 246, .2);
        }

        .rv-chip:hover {
            transform: scale(1.02);
        }

        .rv-chip--FIXA {
            background: var(--rv-fixa-bg);
            color: var(--rv-fixa-text);
            border-color: rgba(91, 156, 246, .2);
        }

        .rv-chip--UNICA {
            background: var(--rv-unica-bg);
            color: var(--rv-unica-text);
            border-color: rgba(251, 191, 36, .2);
        }

        .rv-chip--MENSALISTA {
            background: rgba(139, 92, 246, .08);
            color: #7c3aed;
            border-color: rgba(139, 92, 246, .2);
        }

        [data-theme="dark"] .rv-chip--MENSALISTA {
            background: rgba(167, 139, 250, .12);
            color: #c4b5fd;
            border-color: rgba(167, 139, 250, .2);
        }

        [data-theme="dark"] .rv-chip--UNICA {
            color: #fde68a;
        }

        .rv-chip__dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .rv-chip--FIXA .rv-chip__dot {
            background: var(--accent);
        }

        .rv-chip--UNICA .rv-chip__dot {
            background: var(--warning, #f59e0b);
        }

        .rv-chip--MENSALISTA .rv-chip__dot {
            background: #8b5cf6;
        }

        .rv-chip__name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rv-chip__badge {
            font-size: .58rem;
            font-weight: 700;
            opacity: .65;
            letter-spacing: .3px;
        }

        .rv-livre {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            width: 100%;
            padding: 6px 8px;
            border-radius: 8px;
            border: 1px dashed var(--card-border);
            background: transparent;
            color: var(--t4, #94a3b8);
            font-family: inherit;
            font-size: .72rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
        }

        .rv-livre:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(91, 156, 246, .04);
        }

        .rv-multi-slot {
            background: repeating-linear-gradient(135deg, transparent, transparent 3px, var(--card-border) 3px, var(--card-border) 5px);
            opacity: .3;
            height: 3px;
            border-radius: 2px;
            margin: 14px 6px;
        }

        .rv-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 0;
        }

        .rv-spinner {
            width: 28px;
            height: 28px;
            border: 3px solid var(--card-border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .rv-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--t3);
        }

        .rv-empty i {
            font-size: 2rem;
            opacity: .4;
            margin-bottom: 10px;
            display: block;
        }

        .rv-legend {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 12px 0;
            margin-top: 10px;
        }

        .rv-legend__item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rv-legend__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .rv-legend__dot--FIXA {
            background: var(--accent);
        }

        .rv-legend__dot--UNICA {
            background: var(--warning, #f59e0b);
        }

        .rv-legend__dot--MENSALISTA {
            background: #8b5cf6;
        }

        .rv-legend__dot--livre {
            background: var(--bg);
            border: 1px dashed var(--t4, #94a3b8);
        }

        .rv-legend__text {
            font-size: .7rem;
            font-weight: 600;
            color: var(--t3);
            letter-spacing: .3px;
        }

        .rv-td-today {
            font-weight: 700 !important;
            color: var(--accent) !important;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all .2s;
            backdrop-filter: blur(4px);
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
            box-shadow: 0 25px 50px rgba(0, 0, 0, .2);
            transform: translateY(20px);
            transition: transform .2s;
        }

        .modal-overlay.is-open .modal-box {
            transform: translateY(0);
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--card-border);
        }

        .modal-head__title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
        }

        .modal-head__close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: var(--bg);
            color: var(--t3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .modal-body {
            padding: 16px 20px;
        }

        .field {
            margin-bottom: 12px;
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t2);
            margin-bottom: 4px;
        }

        .req {
            color: var(--danger, #ef4444);
        }

        .sdb-input,
        .sdb-select {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: var(--card);
            color: var(--t1);
            font-family: inherit;
            font-size: .85rem;
            outline: none;
            transition: border-color .15s;
        }

        .sdb-input:focus,
        .sdb-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--input-glow, rgba(91, 156, 246, .12));
        }

        .modal-info-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .modal-info-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 8px;
            background: rgba(91, 156, 246, .08);
            color: var(--accent);
            font-size: .78rem;
            font-weight: 600;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .modal-foot {
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            border-top: 1px solid var(--card-border);
        }

        .modal-foot__left {
            display: flex;
            gap: 8px;
        }

        .modal-foot__right {
            display: flex;
            gap: 8px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-ghost {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t2);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            background: var(--danger, #ef4444);
            color: #fff;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .5s linear infinite;
        }

        .cli-search-wrap {
            position: relative;
        }

        .cli-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
        }

        .cli-results.is-open {
            display: block;
        }

        .cli-result-item {
            padding: 10px 14px;
            cursor: pointer;
            font-size: .84rem;
            color: var(--t1);
            border-bottom: 1px solid var(--card-border);
        }

        .cli-result-item:hover {
            background: var(--hover);
        }

        .cli-result-item:last-child {
            border-bottom: none;
        }

        .cli-result-item small {
            display: block;
            font-size: .72rem;
            color: var(--t3);
            margin-top: 2px;
        }

        .cli-selected {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(91, 156, 246, .06);
            border: 1px solid rgba(91, 156, 246, .15);
        }

        .cli-selected__nome {
            flex: 1;
            font-size: .85rem;
            font-weight: 600;
            color: var(--t1);
        }

        .cli-selected__clear {
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            color: var(--t3);
            cursor: pointer;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cli-selected__clear:hover {
            color: var(--danger, #ef4444);
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 110;
            opacity: 0;
            visibility: hidden;
            transition: all .2s;
            backdrop-filter: blur(4px);
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
            max-width: 380px;
            text-align: center;
        }

        .confirm-box__icon {
            font-size: 1.6rem;
            color: var(--warning, #f59e0b);
            margin-bottom: 12px;
        }

        .confirm-box__msg {
            font-size: .88rem;
            color: var(--t1);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .confirm-box__actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: var(--card);
            color: var(--t3);
            text-decoration: none;
            cursor: pointer;
            transition: all .15s;
            font-size: .9rem;
        }

        .act-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        @media (max-width: 768px) {
            .rv-toolbar__row {
                gap: 8px;
            }

            .rv-toolbar__sep {
                display: none;
            }

            .rv-stats {
                flex-wrap: wrap;
            }

            .rv-stat {
                min-width: 90px;
            }

            .rv-days {
                gap: 4px;
                flex-wrap: wrap;
            }

            .rv-day {
                padding: 6px 10px;
                min-width: 46px;
            }

            .field-row {
                grid-template-columns: 1fr;
            }

            .rv-lanes {
                gap: 8px;
            }

            .rv-lane {
                min-width: 160px;
            }
        }

        @media (max-width: 480px) {
            .rv-toolbar {
                padding: 10px 12px;
            }

            .rv-day__num {
                font-size: .88rem;
            }

            .rv-day__abbr {
                font-size: .6rem;
            }

            .rv-chip {
                font-size: .7rem;
                padding: 4px 6px;
            }

            .modal-box {
                max-width: 100%;
            }

            .rv-lane {
                min-width: 150px;
            }

            .rv-tipo-chips {
                flex-wrap: wrap;
            }
        }

        .rv-view-toggle {
            display: inline-flex;
            gap: 2px;
            background: var(--bg);
            border-radius: 8px;
            padding: 2px;
        }

        .rv-view-btn {
            padding: 5px 14px;
            border: none;
            border-radius: 6px;
            background: transparent;
            color: var(--t3);
            font-family: inherit;
            font-size: .8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
        }

        .rv-view-btn.active {
            background: var(--card);
            color: var(--t1);
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
        }

        [data-theme="dark"] .rv-view-btn.active {
            background: var(--accent);
            color: #fff;
        }

        .rv-slot--selected {
            background: rgba(91, 156, 246, .08) !important;
        }

        .rv-slot--selected .rv-slot__time {
            color: var(--accent) !important;
        }

        .rv-livre--selected {
            border: 1px solid var(--accent) !important;
            background: rgba(91, 156, 246, .06) !important;
            color: var(--accent) !important;
            font-weight: 600 !important;
        }

        .rv-sel-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 12px 20px;
            display: none;
            align-items: center;
            gap: 14px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .2);
            z-index: 50;
            max-width: 600px;
            width: calc(100% - 40px);
        }

        .rv-sel-bar.is-open {
            display: flex;
        }

        [data-theme="dark"] .rv-sel-bar {
            background: #0f172a;
            border-color: rgba(255, 255, 255, .1);
        }

        .rv-sel-bar__info {
            flex: 1;
            min-width: 0;
        }

        .rv-sel-bar__title {
            font-size: .82rem;
            font-weight: 700;
            color: var(--t1);
        }

        .rv-sel-bar__detail {
            font-size: .74rem;
            color: var(--t3);
            margin-top: 2px;
        }

        .rv-sel-bar__btn-clear {
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t2);
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
        }

        .rv-sel-bar__btn-go {
            padding: 8px 18px;
            border-radius: 8px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(91, 156, 246, .3);
        }

        .rv-month {
            max-width: 700px;
            margin: 0 auto;
            background: var(--card);
            border-radius: 14px;
            border: 1px solid var(--card-border);
            overflow: hidden;
        }

        .rv-month__grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .rv-month__hdr {
            padding: 10px 4px;
            text-align: center;
            font-size: .72rem;
            font-weight: 700;
            color: var(--t3);
            text-transform: uppercase;
            letter-spacing: .5px;
            background: var(--bg);
            border-bottom: 2px solid var(--card-border);
        }

        .rv-month__cell {
            padding: 8px 6px;
            min-height: 72px;
            border-bottom: 1px solid var(--card-border);
            border-right: 1px solid var(--card-border);
            cursor: pointer;
            transition: background .15s;
        }

        .rv-month__cell:nth-child(7n) {
            border-right: none;
        }

        .rv-month__cell:hover {
            background: var(--hover);
        }

        .rv-month__cell--empty {
            background: var(--bg);
            cursor: default;
        }

        .rv-month__cell--empty:hover {
            background: var(--bg);
        }

        .rv-month__day {
            font-weight: 600;
            font-size: .8rem;
            color: var(--t1);
            margin-bottom: 4px;
        }

        .rv-month__day--today {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
        }

        .rv-month__badge {
            font-size: .62rem;
            padding: 1px 5px;
            border-radius: 3px;
            font-weight: 600;
            display: inline-block;
        }

        .rv-month__badge--fixas {
            background: rgba(91, 156, 246, .1);
            color: var(--accent);
        }

        .rv-month__badge--unicas {
            background: rgba(251, 191, 36, .1);
            color: var(--warning, #f59e0b);
        }

        .rv-month__badge--mensalistas {
            background: rgba(139, 92, 246, .1);
            color: #8b5cf6;
        }

        .rv-days {
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .rv-sel-bar {
                bottom: 10px;
                padding: 10px 14px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .rv-sel-bar__title {
                font-size: .78rem;
            }

            .rv-month__cell {
                min-height: 56px;
                padding: 6px 4px;
            }
        }

        @media (max-width: 480px) {
            .rv-sel-bar {
                flex-direction: column;
                text-align: center;
            }

            .rv-sel-bar__btn-go {
                width: 100%;
                justify-content: center;
            }
        }

        @media print {

            .topnav,
            .bottomnav,
            .rv-toolbar,
            .rv-days,
            .rv-stats,
            .rv-legend,
            .act-btn,
            .modal-overlay,
            .confirm-overlay,
            .rv-livre,
            .page-header__sub,
            .rv-lane__foot,
            .rv-sel-bar,
            .rv-view-toggle {
                display: none !important;
            }

            .page {
                padding-top: 0 !important;
            }

            .page-header {
                padding: 0 !important;
                margin-bottom: 8px !important;
            }

            .rv-lane {
                border: 1px solid #999 !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .rv-chip {
                font-size: 7px !important;
                padding: 2px 4px !important;
            }

            .rv-lanes {
                overflow: visible !important;
                flex-wrap: wrap !important;
            }

            .rv-lane {
                max-width: none !important;
                min-width: 0 !important;
            }

            .rv-lane__body {
                max-height: none !important;
                overflow: visible !important;
            }
        }
    </style>
@endsection

@section('content')
    <div id="rvLoading" class="rv-loading" style="display:none;">
        <div class="rv-spinner"></div>
    </div>

    <div id="rvPanel">
        <div class="rv-toolbar">
            <div class="rv-toolbar__row">
                <div class="rv-tabs" id="rvTabs"></div>
                <div class="rv-toolbar__sep"></div>
                <div class="rv-view-toggle">
                    <button type="button" class="rv-view-btn active" data-view="semana"
                        onclick="trocarView('semana')">Semana</button>
                    <button type="button" class="rv-view-btn" data-view="mes" onclick="trocarView('mes')">Mês</button>
                </div>
                <div class="rv-toolbar__sep"></div>
                <button type="button" class="rv-nav-btn" onclick="navPeriodo(-1)"><i
                        class="fi fi-rr-angle-left"></i></button>
                <span class="rv-week-label" id="rvWeekLabel"></span>
                <button type="button" class="rv-nav-btn" onclick="navPeriodo(1)"><i
                        class="fi fi-rr-angle-right"></i></button>
                <button type="button" class="rv-today-btn" id="rvTodayBtn" style="display:none"
                    onclick="irParaHoje()">Hoje</button>
            </div>
            <div class="rv-toolbar__row">
                <div class="rv-tipo-chips">
                    <button type="button" class="rv-tipo-chip active" data-tipo="" onclick="filtrarTipo(this)"><i
                            class="fi fi-rr-list"></i> Todos</button>
                    <button type="button" class="rv-tipo-chip" data-tipo="FIXA" onclick="filtrarTipo(this)"><i
                            class="fi fi-rr-refresh"></i> Fixas</button>
                    <button type="button" class="rv-tipo-chip" data-tipo="UNICA" onclick="filtrarTipo(this)"><i
                            class="fi fi-rr-calendar-day"></i> Únicas</button>
                    <button type="button" class="rv-tipo-chip" data-tipo="MENSALISTA" onclick="filtrarTipo(this)"><i
                            class="fi fi-rr-user"></i> Mensalistas</button>
                </div>
                <input type="text" class="rv-search" id="rvBusca"
                    placeholder="Buscar por nome do cliente ou observação..." oninput="debounceBusca()">
            </div>
        </div>

        <div id="rvDaySelector" class="rv-days"></div>
        <div id="rvStats" class="rv-stats"></div>
        <div id="rvGridContainer"></div>

        <div class="rv-legend">
            <div class="rv-legend__item"><span class="rv-legend__dot rv-legend__dot--FIXA"></span><span
                    class="rv-legend__text">FIXA</span></div>
            <div class="rv-legend__item"><span class="rv-legend__dot rv-legend__dot--UNICA"></span><span
                    class="rv-legend__text">ÚNICA</span></div>
            <div class="rv-legend__item"><span class="rv-legend__dot rv-legend__dot--MENSALISTA"></span><span
                    class="rv-legend__text">MENSALISTA</span></div>
            <div class="rv-legend__item"><span class="rv-legend__dot rv-legend__dot--livre"></span><span
                    class="rv-legend__text">LIVRE</span></div>
        </div>
    </div>

    <div class="rv-sel-bar" id="rvSelBar">
        <div class="rv-sel-bar__info">
            <div class="rv-sel-bar__title" id="rvSelTitle"></div>
            <div class="rv-sel-bar__detail" id="rvSelDetail"></div>
        </div>
        <button type="button" class="rv-sel-bar__btn-clear" onclick="limparSelecao()">Limpar</button>
        <button type="button" class="rv-sel-bar__btn-go" onclick="confirmarSelecao()"><i class="fi fi-rr-check"></i>
            Reservar</button>
    </div>

    <div class="modal-overlay" id="modalReserva" onclick="if(event.target===this)fecharModal()">
        <div class="modal-box">
            <div class="modal-head">
                <span class="modal-head__title" id="modalTitulo">Nova Reserva</span>
                <button type="button" class="modal-head__close" onclick="fecharModal()"><i
                        class="fi fi-rr-cross-small"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reserva-id">
                <input type="hidden" id="reserva-dia">
                <input type="hidden" id="reserva-horario">

                <div id="modalInfoChips" class="modal-info-chips"></div>

                <div id="modalMultiSlotWarning" class="field" style="display:none">
                    <div
                        style="padding:10px 14px;border-radius:10px;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);font-size:.78rem;color:var(--warning,#d97706)">
                        <strong>Atenção:</strong> <span id="modalMultiSlotMsg"></span>
                    </div>
                </div>

                <div class="field" id="fieldArea" style="display:none">
                    <label class="sdb-label">Área <span class="req">*</span></label>
                    <select id="reserva-area" class="sdb-select">
                        <option value="">Selecione...</option>
                        @foreach ($areasPorTipo as $grupo)
                            <optgroup label="{{ $grupo['tipo']->nome }}">
                                @foreach ($grupo['areas'] as $a)
                                    <option value="{{ $a->id }}">{{ $a->nome }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="sdb-label">Cliente <span class="req">*</span></label>
                    <input type="hidden" id="reserva-cliente-id">
                    <div class="cli-search-wrap">
                        <input type="text" id="reserva-cliente-busca" class="sdb-input"
                            placeholder="Buscar cliente por nome ou telefone..." oninput="buscarCliente()">
                        <div class="cli-results" id="cliResults"></div>
                    </div>
                    <div class="cli-selected" id="cliSelected">
                        <span class="cli-selected__nome" id="cliSelectedNome"></span>
                        <button type="button" class="cli-selected__clear" onclick="limparCliente()"><i
                                class="fi fi-rr-cross-small"></i></button>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label class="sdb-label">Tipo <span class="req">*</span></label>
                        <select id="reserva-tipo" class="sdb-select" onchange="onTipoChange()">
                            <option value="UNICA">Única</option>
                            <option value="FIXA">Fixa</option>
                            <option value="MENSALISTA">Mensalista</option>
                        </select>
                    </div>
                    <div class="field" id="fieldDuracao" style="display:none">
                        <label class="sdb-label">Duração real (min)</label>
                        <input type="number" id="reserva-duracao-min" class="sdb-input" placeholder="Ex: 90">
                    </div>
                </div>

                <div class="field" id="fieldDataReserva" style="display:none">
                    <label class="sdb-label">Data da Reserva <span class="req">*</span></label>
                    <input type="date" id="reserva-data" class="sdb-input">
                </div>

                <div class="field" id="fieldPeriodoFixa" style="display:none">
                    <label class="sdb-label">Período de Vigência</label>
                    <div class="field-row">
                        <input type="date" id="reserva-data-inicio" class="sdb-input" placeholder="Início">
                        <input type="date" id="reserva-data-fim" class="sdb-input" placeholder="Fim">
                    </div>
                </div>

                <div class="field">
                    <label class="sdb-label">Observações</label>
                    <textarea id="reserva-obs" class="sdb-input" style="min-height:60px;resize:vertical"
                        placeholder="Anotações opcionais..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <div class="modal-foot__left">
                    @can('reservas.excluir')
                        <button type="button" class="btn-danger" id="btnExcluir" style="display:none"
                            onclick="excluirReserva()"><i class="fi fi-rr-trash"></i> Excluir</button>
                    @endcan
                </div>
                <div class="modal-foot__right">
                    <button type="button" class="btn-ghost" onclick="fecharModal()">Cancelar</button>
                    <button type="button" class="btn-primary" id="btnSalvar" onclick="salvarReserva()"><i
                            class="fi fi-rr-check"></i> Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-box__icon"><i class="fi fi-rr-exclamation"></i></div>
            <div class="confirm-box__msg" id="confirmMsg">Confirma a ação?</div>
            <div class="confirm-box__actions">
                <button type="button" class="btn-ghost" onclick="fecharConfirm(false)">Cancelar</button>
                <button type="button" class="btn-danger" onclick="fecharConfirm(true)">Confirmar</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            'use strict';

            var CSRF = '{{ csrf_token() }}';
            var ROUTE_STORE = '{{ route('reservas.store') }}';
            var ROUTE_UPDATE = '{{ route('reservas.update', '__ID__') }}';
            var ROUTE_DESTROY = '{{ route('reservas.destroy', '__ID__') }}';
            var ROUTE_DATA = '{{ route('reservas.data') }}';
            var ROUTE_BUSCAR_CLIENTE = '{{ route('clientes.buscar') }}';
            var ROUTE_EXPORT_PDF = '{{ route('reservas.exportar.pdf.filtrado') }}';
            var ROUTE_EXPORT_XLSX = '{{ route('reservas.exportar.xlsx.filtrado') }}';

            var canCriar = {{ auth()->user()->can('reservas.criar') ? 'true' : 'false' }};
            var canEditar = {{ auth()->user()->can('reservas.editar') ? 'true' : 'false' }};

            var todasAreas = {{ Js::from($todasAreasJson ?? []) }};
            var areasPorTipo =
                {{ Js::from(
                    $areasPorTipo->map(
                            fn($g) => [
                                'tipo_id' => $g['tipo']->id,
                                'tipo_nome' => $g['tipo']->nome,
                                'tipo_icone' => $g['tipo']->icone,
                                'tipo_cor' => $g['tipo']->cor,
                                'areas' => $g['areas']->map(
                                        fn($a) => [
                                            'id' => $a->id,
                                            'nome' => $a->nome,
                                            'descricao' => $a->descricao,
                                            'modo_reserva' => $a->modo_reserva,
                                            'tipo_area_id' => $a->tipo_area_id,
                                        ],
                                    )->values(),
                            ],
                        )->values(),
                ) }};
            var areaIdInicial = {{ $areaIdInicial ?? 'null' }};

            var DIAS = [{
                    key: 'SEGUNDA',
                    label: 'Segunda',
                    abbr: 'SEG'
                },
                {
                    key: 'TERCA',
                    label: 'Terça',
                    abbr: 'TER'
                },
                {
                    key: 'QUARTA',
                    label: 'Quarta',
                    abbr: 'QUA'
                },
                {
                    key: 'QUINTA',
                    label: 'Quinta',
                    abbr: 'QUI'
                },
                {
                    key: 'SEXTA',
                    label: 'Sexta',
                    abbr: 'SEX'
                },
                {
                    key: 'SABADO',
                    label: 'Sábado',
                    abbr: 'SÁB'
                },
                {
                    key: 'DOMINGO',
                    label: 'Domingo',
                    abbr: 'DOM'
                }
            ];
            var DIAS_KEY = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];
            var MESES = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
                'Outubro', 'Novembro', 'Dezembro'
            ];

            function resolverTipoIdxInicial() {
                if (!areaIdInicial || !areasPorTipo.length) return 0;
                for (var i = 0; i < areasPorTipo.length; i++) {
                    for (var j = 0; j < areasPorTipo[i].areas.length; j++) {
                        if (areasPorTipo[i].areas[j].id === areaIdInicial) return i;
                    }
                }
                return 0;
            }

            var state = {
                tipoIdx: resolverTipoIdxInicial(),
                diaSel: DIAS_KEY[(new Date().getDay() + 6) % 7],
                semanaOffset: 0,
                viewMode: 'semana',
                mesAtual: new Date().getMonth(),
                anoAtual: new Date().getFullYear(),
                filtroTipo: '',
                filtroBusca: '',
                reservas: {{ Js::from($reservasIniciais) }},
                loading: false,
                _filtered: null,
                sel: null
            };

            var reservaMap = {};
            var debounceTimer = null;
            var cliSearchTimer = null;
            var currentAbort = null;
            var confirmCallback = null;
            var _escDiv = document.createElement('div');
            var _modalBodyHtml = null;

            var $loading = document.getElementById('rvLoading');
            var $tabs = document.getElementById('rvTabs');
            var $weekLabel = document.getElementById('rvWeekLabel');
            var $todayBtn = document.getElementById('rvTodayBtn');
            var $daySelector = document.getElementById('rvDaySelector');
            var $stats = document.getElementById('rvStats');
            var $gridContainer = document.getElementById('rvGridContainer');
            var $busca = document.getElementById('rvBusca');
            var $btnPdf = document.getElementById('btnExportPdf');
            var $btnXlsx = document.getElementById('btnExportXlsx');
            var $selBar = document.getElementById('rvSelBar');
            var $selTitle = document.getElementById('rvSelTitle');
            var $selDetail = document.getElementById('rvSelDetail');

            var $modalOverlay = document.getElementById('modalReserva');
            var $modalTitulo = document.getElementById('modalTitulo');
            var $modalInfoChips = document.getElementById('modalInfoChips');
            var $confirmOverlay = document.getElementById('confirmOverlay');
            var $btnExcluir = document.getElementById('btnExcluir');
            var $btnSalvar = document.getElementById('btnSalvar');

            function $el(id) {
                return document.getElementById(id);
            }

            function rebindModalEls() {
                return {
                    rId: $el('reserva-id'),
                    rDia: $el('reserva-dia'),
                    rHorario: $el('reserva-horario'),
                    rClienteId: $el('reserva-cliente-id'),
                    rClienteBusca: $el('reserva-cliente-busca'),
                    cliResults: $el('cliResults'),
                    cliSelected: $el('cliSelected'),
                    cliSelectedNome: $el('cliSelectedNome'),
                    rTipo: $el('reserva-tipo'),
                    rData: $el('reserva-data'),
                    rDataInicio: $el('reserva-data-inicio'),
                    rDataFim: $el('reserva-data-fim'),
                    rObs: $el('reserva-obs'),
                    fieldData: $el('fieldDataReserva'),
                    fieldFixa: $el('fieldPeriodoFixa'),
                    fieldArea: $el('fieldArea'),
                    fieldDuracao: $el('fieldDuracao'),
                    rArea: $el('reserva-area'),
                    rDuracaoMin: $el('reserva-duracao-min'),
                    multiWarn: $el('modalMultiSlotWarning'),
                    multiMsg: $el('modalMultiSlotMsg')
                };
            }
            var m = rebindModalEls();

            function escHtml(s) {
                if (!s) return '';
                _escDiv.textContent = s;
                return _escDiv.innerHTML;
            }

            function fmtIso(d) {
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate())
                    .padStart(2, '0');
            }

            function fmtBr(iso) {
                if (!iso) return '';
                var p = iso.split('-');
                return p[2] + '/' + p[1] + '/' + p[0];
            }

            function getWeekDates() {
                var h = new Date();
                h.setDate(h.getDate() + state.semanaOffset * 7);
                var dow = (h.getDay() + 6) % 7;
                var seg = new Date(h);
                seg.setDate(seg.getDate() - dow);
                var map = {};
                for (var i = 0; i < 7; i++) {
                    var d = new Date(seg);
                    d.setDate(seg.getDate() + i);
                    map[DIAS_KEY[i]] = d;
                }
                return map;
            }

            function getAreasDoTipo() {
                return areasPorTipo.length && areasPorTipo[state.tipoIdx] ? areasPorTipo[state.tipoIdx].areas : [];
            }

            function getModoDoTipo() {
                var a = getAreasDoTipo();
                return a.length ? a[0].modo_reserva : 'HORARIO';
            }

            function getHorariosForArea(areaId, dia) {
                for (var i = 0; i < todasAreas.length; i++) {
                    if (todasAreas[i].id === areaId && todasAreas[i].horarios && todasAreas[i].horarios[dia])
                    return todasAreas[i].horarios[dia];
                }
                return [];
            }

            function indexReservas() {
                reservaMap = {};
                if (!state.reservas) return;
                for (var i = 0; i < state.reservas.length; i++) reservaMap[state.reservas[i].id] = state.reservas[i];
            }

            function getFiltered() {
                if (state._filtered) return state._filtered;
                var r = state.reservas || [];
                if (state.filtroTipo) r = r.filter(function(x) {
                    return x.tipo === state.filtroTipo;
                });
                if (state.filtroBusca) {
                    var t = state.filtroBusca.toLowerCase();
                    r = r.filter(function(x) {
                        return (x.cliente_nome && x.cliente_nome.toLowerCase().indexOf(t) !== -1) || (x.obs && x
                            .obs.toLowerCase().indexOf(t) !== -1);
                    });
                }
                state._filtered = r;
                return r;
            }

            function invalidateFiltered() {
                state._filtered = null;
            }

            function findDiaInteiro(reservas, areaId, dia, cellDate) {
                var results = [];
                for (var i = 0; i < reservas.length; i++) {
                    var r = reservas[i];
                    if (r.area_id !== areaId || r.dia_semana !== dia) continue;
                    if (r.tipo === 'UNICA') {
                        if (cellDate && r.data_reserva === cellDate) results.push(r);
                    } else {
                        if (!cellDate) {
                            results.push(r);
                            continue;
                        }
                        if (r.data_inicio && cellDate < r.data_inicio) continue;
                        if (r.data_fim && cellDate > r.data_fim) continue;
                        results.push(r);
                    }
                }
                return results;
            }

            function fixaVisivelNaData(r, cellDate) {
                if (!cellDate) return true;
                if (r.tipo === 'UNICA') return r.data_reserva === cellDate;
                if (r.data_inicio && cellDate < r.data_inicio) return false;
                if (r.data_fim && cellDate > r.data_fim) return false;
                return true;
            }

            function areaNome(areaId) {
                for (var i = 0; i < todasAreas.length; i++) {
                    if (todasAreas[i].id === areaId) return todasAreas[i].nome;
                }
                return '';
            }

            function diaLbl(key) {
                for (var i = 0; i < DIAS.length; i++) {
                    if (DIAS[i].key === key) return DIAS[i].label;
                }
                return key;
            }

            function findResAtSlot(reservas, areaId, dia, hora, cellDate) {
                for (var i = 0; i < reservas.length; i++) {
                    var r = reservas[i];
                    if (r.area_id !== areaId || r.dia_semana !== dia || !fixaVisivelNaData(r, cellDate)) continue;
                    if (r.horario_inicio === hora) return {
                        type: 'exact',
                        reserva: r
                    };
                    if (r.horario_inicio < hora && r.horario_fim > hora) return {
                        type: 'multi',
                        reserva: r
                    };
                }
                return null;
            }

            function showRvToast(msg, type) {
                if (type === 'error') SdbToast.error(msg);
                else if (type === 'success') SdbToast.success(msg);
                else SdbToast.warning(msg);
            }

            function isSlotSelected(areaId, dia, hora) {
                var s = state.sel;
                return s && s.areaId === areaId && s.dia === dia && s.slots.indexOf(hora) !== -1;
            }

            window.handleSlotClick = function(areaId, dia, hora) {
                if (!canCriar) return;
                var reservas = getFiltered();
                var dates = getWeekDates();
                var cellDate = dates[dia] ? fmtIso(dates[dia]) : null;

                if (findResAtSlot(reservas, areaId, dia, hora, cellDate)) return;

                var area = null;
                for (var i = 0; i < todasAreas.length; i++) {
                    if (todasAreas[i].id === areaId) {
                        area = todasAreas[i];
                        break;
                    }
                }
                if (!area) return;

                var s = state.sel;
                if (!s || s.areaId !== areaId || s.dia !== dia) {
                    state.sel = {
                        areaId: areaId,
                        areaNome: areaNome(areaId),
                        dia: dia,
                        slots: [hora],
                        startHora: hora
                    };
                    showRvToast(areaNome(areaId) + ' · ' + hora +
                        ' selecionado — clique outro horário para expandir ou "Reservar" para confirmar', 'info'
                        );
                    render();
                    return;
                }

                var hrs = getHorariosForArea(areaId, dia);
                var startIdx = hrs.indexOf(s.startHora);
                var endIdx = hrs.indexOf(hora);
                if (startIdx === -1 || endIdx === -1) return;

                var lo = Math.min(startIdx, endIdx);
                var hi = Math.max(startIdx, endIdx);
                var range = hrs.slice(lo, hi + 1);

                var conflicts = [];
                for (var j = 0; j < range.length; j++) {
                    var found = findResAtSlot(reservas, areaId, dia, range[j], cellDate);
                    if (found) conflicts.push(range[j] + ' (' + found.reserva.cliente_nome + ')');
                }

                if (conflicts.length) {
                    showRvToast('Conflito: ' + conflicts.join(', ') + ' — selecione outro intervalo', 'error');
                    return;
                }

                var totalMin = range.length * 60;
                var horas = Math.floor(totalMin / 60);
                var mins = totalMin % 60;
                var durLabel = mins > 0 ? horas + 'h' + mins + 'min' : horas + 'h';

                state.sel = {
                    areaId: areaId,
                    areaNome: areaNome(areaId),
                    dia: dia,
                    slots: range,
                    startHora: s.startHora
                };
                showRvToast(areaNome(areaId) + ' · ' + range[0] + ' — ' + range[range.length - 1] + ' · ' + range
                    .length + ' slots · ' + durLabel, 'success');
                render();
            };

            window.limparSelecao = function() {
                state.sel = null;
                render();
            };

            window.confirmarSelecao = function() {
                var s = state.sel;
                if (!s || !s.slots.length) return;
                var horaFim = s.slots[s.slots.length - 1];
                var hParts = horaFim.split(':');
                var endH = parseInt(hParts[0]) + 1;
                var endTime = endH < 24 ? String(endH).padStart(2, '0') + ':' + hParts[1] : '23:59';
                var dates = getWeekDates();
                var dataIso = dates[s.dia] ? fmtIso(dates[s.dia]) : '';

                restoreModalBody();
                $modalTitulo.textContent = 'Nova Reserva';
                $modalInfoChips.innerHTML = buildInfoChips(s.areaNome, s.dia, s.slots[0], endTime, dataIso);

                m.rId.value = '';
                m.rDia.value = s.dia;
                m.rHorario.value = s.slots[0];
                limparCliente();
                m.rTipo.value = 'UNICA';
                m.rData.value = dataIso;
                m.rDataInicio.value = '';
                m.rDataFim.value = '';
                m.rObs.value = '';
                m.fieldData.style.display = 'block';
                m.fieldFixa.style.display = 'none';
                m.fieldArea.style.display = 'none';
                m.rArea.value = s.areaId;

                if (s.slots.length > 1) {
                    m.fieldDuracao.style.display = 'block';
                    if (m.rDuracaoMin) m.rDuracaoMin.value = s.slots.length * 60;
                    m.multiWarn.style.display = 'block';
                    m.multiMsg.textContent = 'O último slot (' + horaFim + ') pode ser utilizado parcialmente. ' + s
                        .slots.length + ' slots selecionados (' + (s.slots.length * 60) +
                        'min). Ajuste a duração real se necessário.';
                    m.rHorario.setAttribute('data-fim', endTime);
                    m.rHorario.setAttribute('data-slots', s.slots.length);
                } else {
                    m.fieldDuracao.style.display = 'none';
                    m.multiWarn.style.display = 'none';
                    m.rHorario.removeAttribute('data-fim');
                    m.rHorario.removeAttribute('data-slots');
                }

                if ($btnExcluir) $btnExcluir.style.display = 'none';
                $btnSalvar.style.display = 'inline-flex';
                $modalOverlay.classList.add('is-open');
                setTimeout(function() {
                    m.rClienteBusca.focus();
                }, 300);
            };

            function renderSelBar() {
                var s = state.sel;
                if (!s || !s.slots.length) {
                    $selBar.classList.remove('is-open');
                    return;
                }
                $selBar.classList.add('is-open');
                $selTitle.textContent = s.areaNome + ' · ' + diaLbl(s.dia);
                var detail = s.slots[0];
                if (s.slots.length > 1) detail += ' — ' + s.slots[s.slots.length - 1];
                detail += ' · ' + s.slots.length + ' slot' + (s.slots.length > 1 ? 's' : '');
                $selDetail.textContent = detail;
            }

            function renderTabs() {
                var h = '';
                for (var i = 0; i < areasPorTipo.length; i++) {
                    var g = areasPorTipo[i];
                    h += '<button type="button" class="rv-tab' + (i === state.tipoIdx ? ' active' : '') +
                        '" onclick="selecionarTipo(' + i + ')">';
                    h += '<i class="fi ' + escHtml(g.tipo_icone) + '" style="color:' + escHtml(g.tipo_cor) + '"></i> ';
                    h += escHtml(g.tipo_nome) + '</button>';
                }
                $tabs.innerHTML = h;
            }

            function renderWeekNav() {
                if (state.viewMode === 'semana') {
                    var dates = getWeekDates();
                    var seg = dates['SEGUNDA'],
                        dom = dates['DOMINGO'];
                    var fmt = function(d) {
                        return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2,
                            '0');
                    };
                    $weekLabel.textContent = fmt(seg) + ' — ' + fmt(dom) + '/' + dom.getFullYear();
                    $todayBtn.style.display = state.semanaOffset !== 0 ? 'inline-flex' : 'none';
                } else {
                    $weekLabel.textContent = MESES[state.mesAtual] + ' ' + state.anoAtual;
                    var hoje = new Date();
                    $todayBtn.style.display = (state.mesAtual !== hoje.getMonth() || state.anoAtual !== hoje
                        .getFullYear()) ? 'inline-flex' : 'none';
                }
            }

            function renderDaySelector() {
                if (state.viewMode === 'mes' || getModoDoTipo() === 'DIA_INTEIRO') {
                    $daySelector.style.display = 'none';
                    return;
                }
                $daySelector.style.display = 'flex';
                var dates = getWeekDates();
                var hojeStr = fmtIso(new Date());
                var reservas = getFiltered();
                var h = '';
                for (var i = 0; i < DIAS.length; i++) {
                    var d = DIAS[i],
                        dt = dates[d.key];
                    var isToday = dt && fmtIso(dt) === hojeStr;
                    var isSel = d.key === state.diaSel;
                    var count = 0;
                    for (var j = 0; j < reservas.length; j++) {
                        if (reservas[j].dia_semana === d.key) count++;
                    }
                    h += '<button type="button" class="rv-day' + (isSel ? ' active' : '') + (isToday && !isSel ?
                        ' today' : '') + '" onclick="selecionarDia(\'' + d.key + '\')">';
                    h += '<span class="rv-day__abbr">' + d.abbr + '</span>';
                    h += '<span class="rv-day__num">' + (dt ? dt.getDate() : '') + '</span>';
                    if (count > 0) h += '<span class="rv-day__count">' + count + '</span>';
                    h += '</button>';
                }
                $daySelector.innerHTML = h;
            }

            function renderStats() {
                if (state.viewMode === 'mes') {
                    $stats.innerHTML = '';
                    return;
                }
                var modo = getModoDoTipo();
                var areas = getAreasDoTipo();
                var reservas = getFiltered();
                var livres = 0,
                    ocupados = 0;

                if (modo === 'HORARIO') {
                    var dates = getWeekDates();
                    var cellDate = dates[state.diaSel] ? fmtIso(dates[state.diaSel]) : null;
                    for (var ai = 0; ai < areas.length; ai++) {
                        var hrs = getHorariosForArea(areas[ai].id, state.diaSel);
                        for (var hi = 0; hi < hrs.length; hi++) {
                            if (findResAtSlot(reservas, areas[ai].id, state.diaSel, hrs[hi], cellDate)) ocupados++;
                            else livres++;
                        }
                    }
                } else {
                    var dates2 = getWeekDates();
                    for (var di = 0; di < DIAS.length; di++) {
                        var cd = dates2[DIAS[di].key] ? fmtIso(dates2[DIAS[di].key]) : null;
                        for (var ai2 = 0; ai2 < areas.length; ai2++) {
                            if (findDiaInteiro(reservas, areas[ai2].id, DIAS[di].key, cd).length) ocupados++;
                            else livres++;
                        }
                    }
                }

                var total = livres + ocupados;
                var pct = total > 0 ? Math.round((ocupados / total) * 100) : 0;
                $stats.innerHTML = '<div class="rv-stat rv-stat--green"><span class="rv-stat__num">' + livres +
                    '</span><span class="rv-stat__label">Livres</span></div>' +
                    '<div class="rv-stat rv-stat--blue"><span class="rv-stat__num">' + ocupados +
                    '</span><span class="rv-stat__label">Ocupados</span></div>' +
                    '<div class="rv-stat rv-stat--amber"><span class="rv-stat__num">' + pct +
                    '%</span><span class="rv-stat__label">Ocupação</span></div>';
            }

            function renderChip(r) {
                var tipo = escHtml(r.tipo);
                var action = canEditar ? 'abrirModalEditar(' + r.id + ')' : 'verDetalhe(' + r.id + ')';
                return '<button type="button" class="rv-chip rv-chip--' + tipo + '" onclick="' + action + '" title="' +
                    escHtml(r.cliente_nome) + ' (' + tipo + ')">' +
                    '<span class="rv-chip__dot"></span><span class="rv-chip__name">' + escHtml(r.cliente_nome) +
                    '</span>' +
                    '<span class="rv-chip__badge">' + tipo.substring(0, 3) + '</span></button>';
            }

            function renderLanes() {
                var areas = getAreasDoTipo(),
                    dia = state.diaSel;
                var reservas = getFiltered();
                var dates = getWeekDates();
                var cellDate = dates[dia] ? fmtIso(dates[dia]) : null;

                if (!areas.length) {
                    $gridContainer.innerHTML =
                        '<div class="rv-empty"><i class="fi fi-rr-info"></i><div>Nenhuma área neste tipo</div></div>';
                    return;
                }

                var anyHorarios = false;
                for (var ci = 0; ci < areas.length; ci++) {
                    if (getHorariosForArea(areas[ci].id, dia).length) {
                        anyHorarios = true;
                        break;
                    }
                }
                if (!anyHorarios) {
                    $gridContainer.innerHTML =
                        '<div class="rv-empty"><i class="fi fi-rr-calendar-xmark"></i><div>Nenhum horário configurado para este dia</div></div>';
                    return;
                }

                var h = '<div class="rv-lanes">';
                for (var ai = 0; ai < areas.length; ai++) {
                    var area = areas[ai];
                    var hrs = getHorariosForArea(area.id, dia);
                    var ocCount = 0;

                    h += '<div class="rv-lane"><div class="rv-lane__head">';
                    h += '<div class="rv-lane__name">' + escHtml(area.nome) + '</div>';
                    if (area.descricao) h += '<div class="rv-lane__desc">' + escHtml(area.descricao) + '</div>';
                    h += '</div><div class="rv-lane__body">';

                    if (!hrs.length) {
                        h +=
                        '<div class="rv-slot"><div class="rv-slot__body" style="text-align:center;color:var(--t4);font-size:.78rem;font-style:italic;padding:12px 0">Sem horários neste dia</div></div>';
                    } else {
                        for (var hi = 0; hi < hrs.length; hi++) {
                            var hora = hrs[hi];
                            var found = findResAtSlot(reservas, area.id, dia, hora, cellDate);
                            var selected = isSlotSelected(area.id, dia, hora);

                            h += '<div class="rv-slot' + (selected ? ' rv-slot--selected' : '') +
                                '"><span class="rv-slot__time">' + hora + '</span><div class="rv-slot__body">';
                            if (found && found.type === 'multi') {
                                h += '<div class="rv-multi-slot"></div>';
                                ocCount++;
                            } else if (found) {
                                h += renderChip(found.reserva);
                                ocCount++;
                            } else {
                                if (canCriar) {
                                    h += '<button type="button" class="rv-livre' + (selected ? ' rv-livre--selected' :
                                            '') + '" onclick="handleSlotClick(' + area.id + ',\'' + dia + '\',\'' +
                                        hora + '\')">';
                                    h += selected ? '<i class="fi fi-rr-check"></i> Selecionado' :
                                        '<i class="fi fi-rr-plus-small"></i> Livre';
                                    h += '</button>';
                                }
                            }
                            h += '</div></div>';
                        }
                    }

                    h += '</div>';
                    h += '<div class="rv-lane__foot">' + ocCount + '/' + hrs.length + ' ocupados</div>';
                    h += '</div>';
                }
                h += '</div>';
                $gridContainer.innerHTML = h;
            }

            function renderLanesDiaInteiro() {
                var areas = getAreasDoTipo(),
                    reservas = getFiltered();
                var dates = getWeekDates(),
                    hoje = fmtIso(new Date());

                if (!areas.length) {
                    $gridContainer.innerHTML =
                        '<div class="rv-empty"><i class="fi fi-rr-info"></i><div>Nenhuma área neste tipo</div></div>';
                    return;
                }

                var h = '<div class="rv-lanes">';
                for (var ai = 0; ai < areas.length; ai++) {
                    var area = areas[ai];
                    h += '<div class="rv-lane" style="min-width:140px"><div class="rv-lane__head">';
                    h += '<div class="rv-lane__name">' + escHtml(area.nome) + '</div>';
                    h += '</div><div class="rv-lane__body">';

                    for (var di = 0; di < DIAS.length; di++) {
                        var d = DIAS[di],
                            dt = dates[d.key];
                        var cellDate = dt ? fmtIso(dt) : null;
                        var isToday = cellDate === hoje;
                        var found = findDiaInteiro(reservas, area.id, d.key, cellDate);

                        h += '<div class="rv-slot' + (isToday ? ' rv-slot--today' : '') + '">';
                        h += '<span class="rv-slot__time" style="min-width:34px">' + d.abbr;
                        if (dt) h += '<br><span style="font-size:.6rem;opacity:.5;font-weight:400">' + dt.getDate() +
                            '/' + (dt.getMonth() + 1) + '</span>';
                        h += '</span><div class="rv-slot__body">';
                        if (found.length) {
                            for (var fi = 0; fi < found.length; fi++) h += renderChip(found[fi]);
                        } else if (canCriar) {
                            h += '<button type="button" class="rv-livre" onclick="abrirModalNovo(\'' + d.key +
                                '\',null,\'' + (cellDate || '') + '\',' + area.id + ')">';
                            h += '<i class="fi fi-rr-plus-small"></i></button>';
                        }
                        h += '</div></div>';
                    }

                    h += '</div></div>';
                }
                h += '</div>';
                $gridContainer.innerHTML = h;
            }

            function renderMonth() {
                var reservas = getFiltered();
                var areas = getAreasDoTipo();
                var areaIds = {};
                for (var ai = 0; ai < areas.length; ai++) areaIds[areas[ai].id] = true;
                var hoje = new Date();
                var primeiro = new Date(state.anoAtual, state.mesAtual, 1);
                var ultimo = new Date(state.anoAtual, state.mesAtual + 1, 0);
                var dowInicio = (primeiro.getDay() + 6) % 7;

                var h = '<div class="rv-month"><div class="rv-month__grid">';
                var hdrDias = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
                for (var hd = 0; hd < 7; hd++) h += '<div class="rv-month__hdr">' + hdrDias[hd] + '</div>';

                for (var e = 0; e < dowInicio; e++) h += '<div class="rv-month__cell rv-month__cell--empty"></div>';

                for (var d = 1; d <= ultimo.getDate(); d++) {
                    var dt = new Date(state.anoAtual, state.mesAtual, d);
                    var dk = DIAS_KEY[(dt.getDay() + 6) % 7];
                    var isToday = d === hoje.getDate() && state.mesAtual === hoje.getMonth() && state.anoAtual === hoje
                        .getFullYear();

                    var fixas = 0,
                        unicas = 0,
                        mensalistas = 0;
                    var isoDate = fmtIso(dt);
                    for (var ri = 0; ri < reservas.length; ri++) {
                        var r = reservas[ri];
                        if (!areaIds[r.area_id]) continue;
                        if (r.dia_semana !== dk || !fixaVisivelNaData(r, isoDate)) continue;
                        if (r.tipo === 'FIXA') fixas++;
                        else if (r.tipo === 'UNICA') unicas++;
                        else mensalistas++;
                    }

                    h += '<div class="rv-month__cell" onclick="irParaDia(' + d + ',' + state.mesAtual + ',' + state
                        .anoAtual + ')">';
                    h += '<div class="rv-month__day' + (isToday ? ' rv-month__day--today' : '') + '">' + d + '</div>';
                    if (fixas) h += '<span class="rv-month__badge rv-month__badge--fixas">' + fixas + ' fixa' + (fixas >
                        1 ? 's' : '') + '</span> ';
                    if (unicas) h += '<span class="rv-month__badge rv-month__badge--unicas">' + unicas + ' única' + (
                        unicas > 1 ? 's' : '') + '</span> ';
                    if (mensalistas) h += '<span class="rv-month__badge rv-month__badge--mensalistas">' + mensalistas +
                        ' mensal</span>';
                    h += '</div>';
                }

                h += '</div></div>';
                $gridContainer.innerHTML = h;
            }

            function render() {
                invalidateFiltered();
                renderTabs();
                renderWeekNav();
                renderDaySelector();
                renderStats();
                renderSelBar();

                document.querySelectorAll('.rv-view-btn').forEach(function(b) {
                    b.classList.toggle('active', b.dataset.view === state.viewMode);
                });

                if (state.viewMode === 'mes') {
                    renderMonth();
                } else if (getModoDoTipo() === 'HORARIO') {
                    renderLanes();
                } else {
                    renderLanesDiaInteiro();
                }
                updatePageSub();
                updateExportLinks();
            }

            function updatePageSub() {
                var grupo = areasPorTipo[state.tipoIdx];
                var reservas = getFiltered();
                var period = state.viewMode === 'mes' ? 'no mês' : 'na semana';
                document.getElementById('pageSub').textContent = (grupo ? grupo.tipo_nome : '') + ' · ' + reservas
                    .length + ' reserva' + (reservas.length !== 1 ? 's' : '') + ' ' + period;
            }

            function updateExportLinks() {
                var params;
                if (state.viewMode === 'mes') {
                    params = 'view=month&data_ref=' + state.anoAtual + '-' + String(state.mesAtual + 1).padStart(2,
                        '0') + '-01';
                } else {
                    var dates = getWeekDates();
                    params = 'view=week&data_ref=' + fmtIso(dates['SEGUNDA']);
                }
                if (state.filtroBusca) params += '&busca=' + encodeURIComponent(state.filtroBusca);
                $btnPdf.href = ROUTE_EXPORT_PDF + '?' + params;
                $btnXlsx.href = ROUTE_EXPORT_XLSX + '?' + params;
            }

            function showLoading(v) {
                $loading.style.display = v ? 'flex' : 'none';
                $gridContainer.style.opacity = v ? '.4' : '1';
            }

            function fetchApi(url, opts) {
                opts = opts || {};
                opts.headers = Object.assign({
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }, opts.headers || {});
                return fetch(url, opts).then(function(r) {
                    if (!r.ok) {
                        var err = new Error('HTTP ' + r.status);
                        err.status = r.status;
                        err.json = function() {
                            return r.json();
                        };
                        throw err;
                    }
                    return r.json();
                });
            }

            function carregarDados() {
                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();

                var dataRef, view;
                if (state.viewMode === 'mes') {
                    dataRef = state.anoAtual + '-' + String(state.mesAtual + 1).padStart(2, '0') + '-01';
                    view = 'month';
                } else {
                    var dates = getWeekDates();
                    dataRef = fmtIso(dates['SEGUNDA']);
                    view = 'week';
                }

                var params = 'view=' + view + '&data_ref=' + dataRef;
                if (state.filtroBusca) params += '&busca=' + encodeURIComponent(state.filtroBusca);

                state.loading = true;
                showLoading(true);

                fetch(ROUTE_DATA + '?' + params, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        signal: currentAbort.signal
                    })
                    .then(function(r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function(data) {
                        state.reservas = data.reservas || [];
                        indexReservas();
                        render();
                    })
                    .catch(function(err) {
                        if (err.name !== 'AbortError') SdbToast.error('Erro ao carregar dados');
                    })
                    .finally(function() {
                        state.loading = false;
                        showLoading(false);
                    });
            }

            window.selecionarTipo = function(idx) {
                state.tipoIdx = idx;
                state.sel = null;
                render();
            };
            window.selecionarDia = function(dia) {
                state.diaSel = dia;
                state.sel = null;
                render();
            };

            window.navPeriodo = function(dir) {
                if (state.viewMode === 'mes') {
                    var m = state.mesAtual + dir;
                    if (m < 0) {
                        state.mesAtual = 11;
                        state.anoAtual--;
                    } else if (m > 11) {
                        state.mesAtual = 0;
                        state.anoAtual++;
                    } else state.mesAtual = m;
                } else {
                    state.semanaOffset += dir;
                }
                state.sel = null;
                carregarDados();
            };

            window.irParaHoje = function() {
                var hoje = new Date();
                state.semanaOffset = 0;
                state.mesAtual = hoje.getMonth();
                state.anoAtual = hoje.getFullYear();
                state.diaSel = DIAS_KEY[(hoje.getDay() + 6) % 7];
                state.sel = null;
                carregarDados();
            };

            window.trocarView = function(v) {
                state.viewMode = v;
                state.sel = null;
                carregarDados();
            };

            window.irParaDia = function(d, m, a) {
                var dt = new Date(a, m, d);
                var hoje = new Date();
                var diff = Math.round((dt - new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate())) /
                    86400000);
                var weekDiff = Math.floor((diff + ((hoje.getDay() + 6) % 7)) / 7);
                state.semanaOffset = weekDiff;
                state.diaSel = DIAS_KEY[(dt.getDay() + 6) % 7];
                state.viewMode = 'semana';
                state.sel = null;
                carregarDados();
            };

            window.filtrarTipo = function(el) {
                state.filtroTipo = el.getAttribute('data-tipo');
                document.querySelectorAll('.rv-tipo-chip').forEach(function(b) {
                    b.classList.toggle('active', b.getAttribute('data-tipo') === state.filtroTipo);
                });
                render();
            };

            window.debounceBusca = function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    state.filtroBusca = $busca.value.trim();
                    carregarDados();
                }, 400);
            };

            function buildInfoChips(areaLabel, diaKey, horInicio, horFim, dataIso) {
                var chips = '<span class="modal-info-chip"><i class="fi fi-rr-marker" style="font-size:.7rem"></i> ' +
                    escHtml(areaLabel) + '</span>';
                chips += '<span class="modal-info-chip"><i class="fi fi-rr-calendar" style="font-size:.7rem"></i> ' +
                    escHtml(diaLbl(diaKey));
                if (dataIso) chips += ' ' + fmtBr(dataIso);
                chips += '</span>';
                if (horInicio) {
                    chips += '<span class="modal-info-chip"><i class="fi fi-rr-clock" style="font-size:.7rem"></i> ' +
                        horInicio;
                    if (horFim && horFim !== horInicio) chips += ' — ' + horFim;
                    chips += '</span>';
                }
                return chips;
            }

            function restoreModalBody() {
                var body = document.querySelector('.modal-body');
                if (_modalBodyHtml === null) {
                    _modalBodyHtml = body.innerHTML;
                } else {
                    body.innerHTML = _modalBodyHtml;
                }
                m = rebindModalEls();
            }

            window.verDetalhe = function(id) {
                var r = reservaMap[id];
                if (!r) return;
                $modalTitulo.textContent = 'Detalhes da Reserva';
                $modalInfoChips.innerHTML = buildInfoChips(r.area_nome || areaNome(r.area_id), r.dia_semana, r
                    .horario_inicio, r.horario_fim, null);
                var body = document.querySelector('.modal-body');
                var h =
                    '<div style="margin-top:8px"><div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">';
                h += '<div style="width:40px;height:40px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;color:var(--t2)">' +
                    escHtml((r.cliente_nome || '?')[0]) + '</div>';
                h += '<div><div style="font-weight:600;color:var(--t1)">' + escHtml(r.cliente_nome) + '</div>';
                if (r.cliente_telefone) h += '<div style="font-size:.78rem;color:var(--t3)">' + escHtml(r
                    .cliente_telefone) + '</div>';
                h += '</div></div><div style="display:grid;gap:8px">';
                h += '<div style="display:flex;justify-content:space-between;padding:8px 12px;border-radius:8px;background:var(--bg)"><span style="color:var(--t3);font-size:.78rem">Tipo</span><span style="font-weight:600;font-size:.82rem;color:var(--t1)">' +
                    escHtml(r.tipo) + '</span></div>';
                if (r.valor_final) h +=
                    '<div style="display:flex;justify-content:space-between;padding:8px 12px;border-radius:8px;background:var(--bg)"><span style="color:var(--t3);font-size:.78rem">Valor</span><span style="font-weight:600;font-size:.82rem;color:var(--t1)">R$ ' +
                    parseFloat(r.valor_final).toFixed(2).replace('.', ',') + '</span></div>';
                if (r.obs) h +=
                    '<div style="padding:8px 12px;border-radius:8px;background:var(--bg)"><span style="color:var(--t3);font-size:.78rem;display:block;margin-bottom:4px">Obs</span><span style="font-size:.82rem;color:var(--t1)">' +
                    escHtml(r.obs) + '</span></div>';
                h += '</div></div>';
                body.innerHTML = h;
                if ($btnExcluir) $btnExcluir.style.display = 'none';
                $btnSalvar.style.display = 'none';
                $modalOverlay.classList.add('is-open');
            };

            window.abrirModalNovo = function(dia, horario, dataIso, areaId) {
                if (!canCriar) return;
                restoreModalBody();
                $modalTitulo.textContent = 'Nova Reserva';
                $modalInfoChips.innerHTML = buildInfoChips(areaNome(areaId), dia, horario, null, dataIso);
                m.rId.value = '';
                m.rDia.value = dia;
                m.rHorario.value = horario || '';
                limparCliente();
                m.rTipo.value = 'UNICA';
                m.rData.value = dataIso || '';
                m.rDataInicio.value = '';
                m.rDataFim.value = '';
                m.rObs.value = '';
                m.fieldData.style.display = 'block';
                m.fieldFixa.style.display = 'none';
                m.fieldArea.style.display = 'none';
                m.rArea.value = areaId || '';
                m.fieldDuracao.style.display = 'none';
                m.multiWarn.style.display = 'none';
                if (horario) {
                    var hp = horario.split(':');
                    var eh = parseInt(hp[0]) + 1;
                    var singleFim = eh < 24 ? String(eh).padStart(2, '0') + ':' + hp[1] : '23:59';
                    m.rHorario.setAttribute('data-fim', singleFim);
                    m.rHorario.setAttribute('data-slots', '1');
                } else {
                    m.rHorario.removeAttribute('data-fim');
                    m.rHorario.removeAttribute('data-slots');
                }
                if ($btnExcluir) $btnExcluir.style.display = 'none';
                $btnSalvar.style.display = 'inline-flex';
                $modalOverlay.classList.add('is-open');
                setTimeout(function() {
                    m.rClienteBusca.focus();
                }, 300);
            };

            window.abrirModalEditar = function(id) {
                var r = reservaMap[id];
                if (!r || !canEditar) return;
                restoreModalBody();
                $modalTitulo.textContent = 'Editar Reserva';
                $modalInfoChips.innerHTML = buildInfoChips(r.area_nome || areaNome(r.area_id), r.dia_semana, r
                    .horario_inicio, r.horario_fim, null);
                m.rId.value = r.id;
                m.rDia.value = r.dia_semana;
                m.rHorario.value = r.horario_inicio || '';
                m.rClienteId.value = r.cliente_id || '';
                if (r.cliente_id && r.cliente_nome) {
                    m.cliSelectedNome.textContent = r.cliente_nome + (r.cliente_telefone ? ' · ' + r
                        .cliente_telefone : '');
                    m.cliSelected.style.display = 'flex';
                    m.rClienteBusca.parentElement.style.display = 'none';
                } else {
                    limparCliente();
                }
                m.rTipo.value = r.tipo || 'UNICA';
                m.rData.value = r.data_reserva || '';
                m.rDataInicio.value = r.data_inicio || '';
                m.rDataFim.value = r.data_fim || '';
                m.rObs.value = r.obs || '';
                m.fieldData.style.display = r.tipo === 'UNICA' ? 'block' : 'none';
                m.fieldFixa.style.display = (r.tipo === 'FIXA' || r.tipo === 'MENSALISTA') ? 'block' : 'none';
                m.fieldArea.style.display = 'none';
                m.rArea.value = r.area_id || '';
                m.fieldDuracao.style.display = 'none';
                m.multiWarn.style.display = 'none';
                if ($btnExcluir) $btnExcluir.style.display = 'inline-flex';
                $btnSalvar.style.display = 'inline-flex';
                $modalOverlay.classList.add('is-open');
            };

            window.fecharModal = function() {
                $modalOverlay.classList.remove('is-open');
            };

            window.onTipoChange = function() {
                var tipo = m.rTipo.value;
                m.fieldData.style.display = tipo === 'UNICA' ? 'block' : 'none';
                m.fieldFixa.style.display = (tipo === 'FIXA' || tipo === 'MENSALISTA') ? 'block' : 'none';
            };

            window.limparCliente = function() {
                m.rClienteId.value = '';
                m.cliSelectedNome.textContent = '';
                m.cliSelected.style.display = 'none';
                m.rClienteBusca.parentElement.style.display = 'block';
                m.rClienteBusca.value = '';
                m.cliResults.classList.remove('is-open');
            };
            window.selecionarCliente = function(id, nome, telefone) {
                m.rClienteId.value = id;
                m.cliSelectedNome.textContent = nome + (telefone ? ' · ' + telefone : '');
                m.cliSelected.style.display = 'flex';
                m.rClienteBusca.parentElement.style.display = 'none';
                m.cliResults.classList.remove('is-open');
            };

            window.buscarCliente = function() {
                clearTimeout(cliSearchTimer);
                var termo = m.rClienteBusca.value.trim();
                if (termo.length < 2) {
                    m.cliResults.classList.remove('is-open');
                    return;
                }
                cliSearchTimer = setTimeout(function() {
                    fetch(ROUTE_BUSCAR_CLIENTE + '?termo=' + encodeURIComponent(termo), {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF
                            }
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            if (!data.length) {
                                m.cliResults.innerHTML =
                                    '<div class="cli-result-item" style="color:var(--t3);cursor:default">Nenhum cliente encontrado</div>';
                            } else {
                                var h = '';
                                for (var i = 0; i < data.length; i++) {
                                    var c = data[i];
                                    h += '<div class="cli-result-item" data-cli-id="' + c.id +
                                        '" data-cli-nome="' + escHtml(c.nome) + '" data-cli-tel="' +
                                        escHtml(c.telefone || '') + '">' + escHtml(c.nome);
                                    if (c.telefone) h += '<small>' + escHtml(c.telefone) + '</small>';
                                    h += '</div>';
                                }
                                m.cliResults.innerHTML = h;
                            }
                            m.cliResults.classList.add('is-open');
                        }).catch(function() {
                            SdbToast.error('Erro ao buscar clientes');
                        });
                }, 300);
            };

            document.addEventListener('click', function(e) {
                var cliRes = document.getElementById('cliResults');
                if (cliRes && cliRes.classList.contains('is-open') && !e.target.closest('.cli-search-wrap'))
                    cliRes.classList.remove('is-open');
                var item = e.target.closest('.cli-result-item[data-cli-id]');
                if (item) selecionarCliente(parseInt(item.dataset.cliId), item.dataset.cliNome, item.dataset
                    .cliTel);
            });

            window.salvarReserva = function() {
                if ($btnSalvar.disabled) return;
                var id = m.rId.value;
                var clienteId = m.rClienteId.value;
                if (!clienteId) {
                    SdbToast.error('Selecione um cliente');
                    return;
                }
                var areaId = m.rArea.value;
                if (!areaId) {
                    SdbToast.error('Selecione a área');
                    return;
                }
                var tipo = m.rTipo.value;
                if (tipo === 'UNICA' && !m.rData.value) {
                    SdbToast.error('Informe a data da reserva');
                    return;
                }

                var dados = {
                    area_id: parseInt(areaId),
                    cliente_id: parseInt(clienteId),
                    dia_semana: m.rDia.value,
                    horario_inicio: m.rHorario.value || null,
                    tipo: tipo,
                    data_reserva: tipo === 'UNICA' ? (m.rData.value || null) : null,
                    data_inicio: (tipo === 'FIXA' || tipo === 'MENSALISTA') ? (m.rDataInicio.value || null) :
                        null,
                    data_fim: (tipo === 'FIXA' || tipo === 'MENSALISTA') ? (m.rDataFim.value || null) : null,
                    obs: m.rObs.value || null
                };

                var fimAttr = m.rHorario.getAttribute('data-fim');
                var slotsAttr = m.rHorario.getAttribute('data-slots');
                if (fimAttr && slotsAttr) {
                    dados.horario_fim = fimAttr;
                    dados.slots_ocupados = parseInt(slotsAttr);
                    var duracaoInput = m.rDuracaoMin ? parseInt(m.rDuracaoMin.value) : null;
                    if (duracaoInput && duracaoInput > 0) dados.duracao_real_min = duracaoInput;
                }

                $btnSalvar.disabled = true;
                $btnSalvar.innerHTML = '<span class="btn-spinner"></span>';

                fetchApi(id ? ROUTE_UPDATE.replace('__ID__', id) : ROUTE_STORE, {
                        method: id ? 'PUT' : 'POST',
                        body: JSON.stringify(dados)
                    })
                    .then(function() {
                        SdbToast.success(id ? 'Reserva atualizada' : 'Reserva criada');
                        fecharModal();
                        state.sel = null;
                        carregarDados();
                    })
                    .catch(function(err) {
                        if (err.status === 422) {
                            err.json().then(function(data) {
                                SdbToast.error(Object.values(data.errors || {}).flat().join(', ') ||
                                    'Dados inválidos');
                            });
                        } else if (err.status === 403) {
                            SdbToast.error('Sem permissão para esta ação');
                        } else {
                            SdbToast.error('Erro ao salvar reserva');
                        }
                    })
                    .finally(function() {
                        $btnSalvar.disabled = false;
                        $btnSalvar.innerHTML = '<i class="fi fi-rr-check"></i> Salvar';
                    });
            };

            window.excluirReserva = function() {
                var id = m.rId.value;
                if (!id) return;
                confirmar('Tem certeza que deseja excluir esta reserva?', function() {
                    fetchApi(ROUTE_DESTROY.replace('__ID__', id), {
                            method: 'DELETE'
                        })
                        .then(function() {
                            SdbToast.success('Reserva excluída');
                            fecharModal();
                            carregarDados();
                        })
                        .catch(function(err) {
                            SdbToast.error(err.status === 403 ? 'Sem permissão' :
                                'Erro ao excluir reserva');
                        });
                });
            };

            function confirmar(msg, cb) {
                document.getElementById('confirmMsg').textContent = msg;
                confirmCallback = cb;
                $confirmOverlay.classList.add('is-open');
            }
            window.fecharConfirm = function(ok) {
                $confirmOverlay.classList.remove('is-open');
                if (ok && confirmCallback) confirmCallback();
                confirmCallback = null;
            };

            _modalBodyHtml = document.querySelector('.modal-body').innerHTML;
            indexReservas();
            render();

        })();
    </script>
@endsection
