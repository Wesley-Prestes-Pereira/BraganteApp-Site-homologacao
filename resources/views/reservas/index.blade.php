@extends('layouts.app')

@section('title', 'Reservas')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title" id="pageTitle">{{ $areaAtiva->nome ?? 'Todas as Áreas' }}</h1>
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

        .week-grid,
        .day-grid,
        .month-grid {
            contain: layout style;
        }

        .week-grid td,
        .month-grid td {
            contain: content;
        }

        .year-month {
            contain: content;
        }

        .rv,
        .cell-add,
        .cell-more,
        .month-cell,
        .view-mode,
        .filter-chip,
        .period-bar__btn,
        .act-btn,
        .btn-primary,
        .btn-ghost,
        .btn-danger,
        .res,
        .h-chip,
        .h-btn-go,
        .h-btn-clr {
            touch-action: manipulation;
        }

        .week-grid th,
        .week-grid .td-hora {
            will-change: transform;
            backface-visibility: hidden;
        }

        :root {
            --rv-fixa-bg: rgba(var(--accent-rgb), .08);
            --rv-fixa-text: var(--accent);
            --rv-fixa-border: var(--accent);
            --rv-fixa-badge: var(--accent);
            --rv-unica-bg: rgba(var(--warning-rgb), .08);
            --rv-unica-text: var(--warning);
            --rv-unica-border: var(--warning);
            --rv-unica-badge: var(--warning);
            --cell-border: var(--card-border);
            --empty-bg: var(--bg);
            --th-bg: var(--card, #0e1526);
            --th-bg-today: var(--card, #0f1a33);
            --td-bg: var(--card, #0c1322);
        }

        [data-theme="dark"] {
            --accent-rgb: 91, 156, 246;
            --warning-rgb: 251, 191, 36;
            --th-bg: #0e1526;
            --th-bg-today: #0f1a33;
            --td-bg: #0c1322;
        }

        [data-theme="light"] {
            --accent-rgb: 59, 130, 246;
            --warning-rgb: 245, 158, 11;
            --rv-fixa-bg: rgba(59, 130, 246, .10);
            --rv-fixa-text: #1d4ed8;
            --rv-unica-bg: rgba(245, 158, 11, .10);
            --rv-unica-text: #92400e;
            --th-bg: #f8fafc;
            --th-bg-today: #eff6ff;
            --td-bg: #ffffff;
            --empty-bg: #fafbfc;
        }

        .act-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: var(--card);
            color: var(--t3);
            cursor: pointer;
            font-size: .95rem;
            text-decoration: none;
        }

        .act-btn:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
            background: var(--hover);
        }

        .area-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .area-filter__label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--t3);
            white-space: nowrap;
        }

        .area-filter__select {
            height: 36px;
            padding: 0 32px 0 12px;
            border-radius: 10px;
            border: 1.5px solid var(--input-border);
            background: var(--input-bg);
            color: var(--t1);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            min-width: 160px;
            max-width: 260px;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        .area-filter__select:hover {
            border-color: var(--input-border-h);
        }

        .area-filter__select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(var(--accent-rgb), .15);
        }

        .area-filter__select,
        .sdb-select,
        .modal-body .sdb-select,
        .sdb-input[type="date"] {
            color-scheme: dark;
        }

        [data-theme="light"] .area-filter__select,
        [data-theme="light"] .sdb-select,
        [data-theme="light"] .modal-body .sdb-select,
        [data-theme="light"] .sdb-input[type="date"] {
            color-scheme: light;
        }

        [data-theme="dark"] select option,
        [data-theme="dark"] .area-filter__select option,
        [data-theme="dark"] .sdb-select option,
        [data-theme="dark"] .modal-body .sdb-select option {
            background: #1a2236;
            color: #eef2f7;
            padding: 8px 12px;
        }

        [data-theme="dark"] select option:hover,
        [data-theme="dark"] select option:checked,
        [data-theme="dark"] .area-filter__select option:checked,
        [data-theme="dark"] .sdb-select option:checked {
            background: #2a3a56;
            color: #fff;
        }

        [data-theme="dark"] select optgroup,
        [data-theme="dark"] .area-filter__select optgroup,
        [data-theme="dark"] .sdb-select optgroup,
        [data-theme="dark"] .modal-body .sdb-select optgroup {
            background: #111827;
            color: #6e809a;
            font-weight: 700;
            font-style: normal;
        }

        [data-theme="light"] select option,
        [data-theme="light"] .area-filter__select option,
        [data-theme="light"] .sdb-select option,
        [data-theme="light"] .modal-body .sdb-select option {
            background: #ffffff;
            color: #0f172a;
            padding: 8px 12px;
        }

        [data-theme="light"] select option:checked,
        [data-theme="light"] .area-filter__select option:checked,
        [data-theme="light"] .sdb-select option:checked {
            background: #e0edff;
            color: #1d4ed8;
        }

        [data-theme="light"] select optgroup,
        [data-theme="light"] .area-filter__select optgroup,
        [data-theme="light"] .sdb-select optgroup,
        [data-theme="light"] .modal-body .sdb-select optgroup {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-style: normal;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            margin-bottom: 14px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
        }

        .toolbar__sep {
            width: 1px;
            height: 28px;
            background: var(--card-border);
        }

        .toolbar .sdb-input {
            max-width: 180px;
            height: 36px;
            font-size: .82rem;
            padding: 0 12px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            color: var(--t1);
            font-family: inherit;
            font-weight: 500;
            outline: none;
        }

        .toolbar .sdb-input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        [data-theme="dark"] .toolbar .sdb-input {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .10);
            color: #eef2f7;
        }

        [data-theme="dark"] .toolbar .sdb-input:focus {
            background: rgba(255, 255, 255, .08);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91, 156, 246, .15);
        }

        [data-theme="dark"] .sdb-input::placeholder,
        [data-theme="dark"] .modal-body .sdb-input::placeholder {
            color: #4a5a74;
        }

        [data-theme="light"] .sdb-input::placeholder,
        [data-theme="light"] .modal-body .sdb-input::placeholder {
            color: #94a3b8;
        }

        .view-modes {
            display: inline-flex;
            gap: 2px;
            background: var(--bg);
            border-radius: 10px;
            padding: 3px;
        }

        .view-mode {
            padding: 6px 14px;
            border: none;
            background: transparent;
            color: var(--t3);
            font-family: inherit;
            font-size: .8rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
        }

        .view-mode:hover {
            color: var(--t1);
        }

        .view-mode.active {
            background: var(--card);
            color: var(--t1);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        }

        [data-theme="dark"] .view-mode.active {
            border-color: rgba(255, 255, 255, .10);
            background: var(--accent);
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t2);
            font-family: inherit;
            font-size: .78rem;
            font-weight: 500;
            cursor: pointer;
        }

        .filter-chip:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .filter-chip.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        [data-theme="dark"] .filter-chip {
            border-color: rgba(255, 255, 255, .10);
        }

        [data-theme="light"] .filter-chip {
            border-color: rgba(0, 0, 0, .12);
        }

        .period-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 14px;
            margin-bottom: 14px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            font-size: .82rem;
            flex-wrap: wrap;
        }

        [data-theme="dark"] .period-bar {
            background: #0c1322;
        }

        .period-bar__label {
            font-weight: 700;
            color: var(--t1);
            white-space: nowrap;
        }

        .period-bar__divider {
            width: 1px;
            height: 20px;
            background: var(--card-border);
        }

        .period-bar__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t2);
            cursor: pointer;
            font-size: .8rem;
        }

        .period-bar__btn:hover {
            background: var(--hover);
            color: var(--accent);
        }

        .period-bar__today {
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid var(--accent);
            background: transparent;
            color: var(--accent);
            cursor: pointer;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
        }

        .period-bar__today:hover {
            background: var(--accent);
            color: #fff;
        }

        .period-bar__count {
            font-size: .75rem;
            color: var(--t3);
        }

        [data-theme="dark"] .period-bar__count {
            color: #6e809a;
        }

        .week-grid {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: auto;
        }

        .week-grid table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
            table-layout: fixed;
        }

        .week-grid th {
            padding: 10px 8px;
            text-align: center;
            font-size: .75rem;
            font-weight: 600;
            color: var(--t2);
            text-transform: uppercase;
            letter-spacing: .3px;
            background: var(--th-bg);
            border-bottom: 1px solid var(--cell-border);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .week-grid th:first-child {
            position: sticky;
            left: 0;
            z-index: 4;
            width: 70px;
            min-width: 70px;
            max-width: 70px;
            background: var(--th-bg);
        }

        .week-grid th small {
            display: block;
            font-weight: 500;
            font-size: .68rem;
            margin-top: 2px;
            opacity: .75;
        }

        .week-grid th.th-today {
            color: var(--accent);
            background: var(--th-bg-today);
        }

        .week-grid td {
            padding: 4px 6px;
            border-bottom: 1px solid var(--card-border);
            border-right: 1px solid var(--card-border);
            vertical-align: top;
            min-height: 50px;
            background: var(--td-bg);
        }

        .week-grid td:last-child {
            border-right: none;
        }

        .week-grid tr:last-child td {
            border-bottom: none;
        }

        .week-grid .td-hora {
            font-size: .82rem;
            font-weight: 600;
            color: var(--t1);
            background: var(--th-bg);
            white-space: nowrap;
            text-align: center;
            position: sticky;
            left: 0;
            z-index: 3;
            width: 70px;
            min-width: 70px;
            max-width: 70px;
            vertical-align: middle;
            border-right: 2px solid var(--cell-border);
        }

        .day-grid {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: clip;
        }

        .day-slot {
            display: flex;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--cell-border);
            min-height: 56px;
        }

        .day-slot:last-child {
            border-bottom: none;
        }

        .day-slot:hover {
            background: var(--hover);
        }

        .day-slot__hora {
            font-size: .85rem;
            font-weight: 600;
            color: var(--t1);
            min-width: 55px;
            padding-top: 4px;
        }

        .day-slot__body {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .day-slot__empty {
            color: var(--t4);
            font-size: .78rem;
            font-style: italic;
            padding-top: 4px;
        }

        .month-grid {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: clip;
        }

        .month-grid table {
            width: 100%;
            border-collapse: collapse;
        }

        .month-grid th {
            padding: 10px 4px;
            text-align: center;
            font-size: .72rem;
            font-weight: 600;
            color: var(--t3);
            text-transform: uppercase;
            letter-spacing: .5px;
            background: var(--th-bg);
            border-bottom: 1px solid var(--cell-border);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .month-grid td {
            width: 14.28%;
            padding: 6px;
            border-bottom: 1px solid var(--card-border);
            border-right: 1px solid var(--card-border);
            vertical-align: top;
            min-height: 80px;
            height: 80px;
            background: var(--td-bg);
        }

        .month-grid td:last-child {
            border-right: none;
        }

        .month-grid tr:last-child td {
            border-bottom: none;
        }

        .month-cell {
            cursor: pointer;
            transition: background .15s;
        }

        .month-cell:hover {
            background: var(--hover);
        }

        .month-grid .month-cell--empty {
            background: var(--empty-bg);
            cursor: default;
        }

        .month-day {
            font-weight: 600;
            font-size: .78rem;
            color: var(--t1);
            margin-bottom: 4px;
        }

        .month-day--today {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
        }

        .month-count {
            font-size: .68rem;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 2px;
        }

        .month-count--fixa {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        .month-count--unica {
            background: rgba(251, 191, 36, .12);
            color: var(--warning);
        }

        .year-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .year-month {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform .2s, border-color .2s, box-shadow .2s;
        }

        .year-month:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .year-month--current {
            border-color: var(--accent);
            box-shadow: 0 0 0 1px var(--accent);
        }

        .year-month__head {
            padding: 8px 12px;
            font-size: .82rem;
            font-weight: 600;
            color: var(--t1);
            background: var(--bg);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .year-month__count {
            font-size: .7rem;
            font-weight: 500;
            color: var(--t3);
        }

        .year-month__body {
            padding: 8px;
        }

        .year-mini-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            font-size: .6rem;
            text-align: center;
        }

        .year-mini-grid__hdr {
            font-size: .6rem;
            font-weight: 700;
            color: var(--t3);
            padding: 2px 0;
        }

        .year-mini-day {
            font-size: .62rem;
            padding: 2px 0;
            border-radius: 3px;
            color: var(--t2);
        }

        .year-mini-day--empty {
            color: transparent;
        }

        .year-mini-day--today {
            background: var(--accent);
            color: #fff;
            font-weight: 700;
        }

        .year-mini-day--has {
            background: rgba(91, 156, 246, .3);
            color: var(--accent);
            font-weight: 700;
        }

        .year-month__stats {
            display: flex;
            gap: 8px;
            padding: 6px 12px;
            border-top: 1px solid var(--card-border);
        }

        .year-month__stat {
            font-size: .68rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .year-month__stat--fixa {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        .year-month__stat--unica {
            background: rgba(251, 191, 36, .12);
            color: var(--warning);
        }

        .rv {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 8px;
            margin-bottom: 4px;
            font-size: .78rem;
            cursor: pointer;
            max-width: 100%;
            overflow: hidden;
            border-left: 3px solid transparent;
            transition: opacity .15s, transform .15s;
        }

        .rv:last-child {
            margin-bottom: 0;
        }

        .rv--fixa {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
            border-left-color: var(--accent);
        }

        .rv--unica {
            background: rgba(251, 191, 36, .12);
            color: var(--warning);
            border-left-color: var(--warning);
        }

        [data-theme="dark"] .rv--unica {
            background: rgba(251, 191, 36, .14);
            color: #fde68a;
        }

        [data-theme="dark"] .rv--unica .rv__name {
            color: #fef3c7;
        }

        [data-theme="dark"] .rv--unica .rv__area {
            color: #fcd34d;
        }

        [data-theme="dark"] .rv--fixa .rv__name {
            color: #dbeafe;
        }

        [data-theme="light"] .rv--fixa {
            background: rgba(59, 130, 246, .10);
            color: #1e40af;
        }

        [data-theme="light"] .rv--fixa .rv__name {
            color: #1e3a5f;
        }

        [data-theme="light"] .rv--unica {
            background: rgba(245, 158, 11, .10);
            color: #92400e;
        }

        [data-theme="light"] .rv--unica .rv__name {
            color: #78350f;
        }

        [data-theme="light"] .rv--unica .rv__area {
            color: #b45309;
        }

        .rv--mensalista {
            background: rgba(139, 92, 246, .08);
            border-left: 3px solid #8b5cf6;
        }

        [data-theme="dark"] .rv--mensalista {
            background: rgba(167, 139, 250, .10);
            border-color: #a78bfa;
        }

        [data-theme="dark"] .rv--mensalista .rv__name {
            color: #c4b5fd;
        }

        [data-theme="dark"] .rv--mensalista .rv__area {
            color: #a78bfa;
        }

        [data-theme="light"] .rv--mensalista {
            background: rgba(139, 92, 246, .06);
        }

        [data-theme="light"] .rv--mensalista .rv__name {
            color: #5b21b6;
        }

        [data-theme="light"] .rv--mensalista .rv__area {
            color: #6d28d9;
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
            color: var(--t2);
            border-bottom: 1px solid var(--card-border);
            transition: background .1s;
        }

        .cli-result-item:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .cli-result-item:last-child {
            border-bottom: none;
        }

        .cli-result-item__tel {
            font-size: .72rem;
            color: var(--t4);
            margin-left: 8px;
        }

        .cli-selected {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--active);
            border-radius: 8px;
            margin-top: 6px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--accent);
        }

        .cli-selected__clear {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--t3);
            cursor: pointer;
            font-size: .82rem;
            display: inline-flex;
        }

        .rv:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        .rv__info {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .rv__area {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .3px;
            color: var(--accent);
            opacity: .85;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .rv__name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .rv__date {
            font-size: .7rem;
            opacity: .8;
        }

        .rv__obs {
            font-size: .68rem;
            opacity: .7;
            font-style: italic;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .rv__badge {
            font-size: .6rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
            flex-shrink: 0;
            letter-spacing: .3px;
        }

        .rv--fixa .rv__badge {
            background: var(--accent);
            color: #fff;
        }

        .rv--unica .rv__badge {
            background: var(--warning);
            color: #422006;
        }

        .cell-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 3px;
            margin-top: 3px;
            border: 1.5px dashed var(--card-border);
            border-radius: 6px;
            background: transparent;
            color: var(--t4);
            font-size: .72rem;
            cursor: pointer;
        }

        .cell-add:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(91, 156, 246, .05);
        }

        .cell-more {
            display: block;
            width: 100%;
            padding: 3px 6px;
            margin-top: 2px;
            font-size: .7rem;
            font-weight: 600;
            color: var(--accent);
            background: rgba(var(--accent-rgb), .08);
            border: 1px dashed rgba(var(--accent-rgb), .3);
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
        }

        .cell-more:hover {
            background: rgba(var(--accent-rgb), .15);
        }

        .cell-overflow {
            margin-top: 4px;
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
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9000;
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s, visibility .25s;
            padding: 16px;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            width: 100%;
            max-width: 440px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            transform: translateY(20px) scale(.97);
            opacity: 0;
            transition: transform .3s, opacity .25s;
        }

        .modal-overlay.is-open .modal-box {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--card-border);
        }

        .modal-head__title {
            font-weight: 700;
            font-size: .95rem;
            color: var(--t1);
        }

        .modal-head__close {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: var(--hover);
            border-radius: 8px;
            color: var(--t3);
            cursor: pointer;
            font-size: 1rem;
        }

        .modal-head__close:hover {
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--card-border);
            gap: 8px;
        }

        .modal-foot__left {
            flex-shrink: 0;
        }

        .modal-foot__right {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        .field {
            margin-bottom: 14px;
        }

        .field:last-child {
            margin-bottom: 0;
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
            color: var(--t2);
            margin-bottom: 6px;
        }

        .req {
            color: var(--danger);
        }

        [data-theme="dark"] .req {
            color: #f87171;
        }

        [data-theme="light"] .req {
            color: #dc2626;
        }

        .modal-body .sdb-input,
        .modal-body .sdb-select {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            background: var(--input-bg);
            color: var(--t1);
            font-family: inherit;
            font-size: .84rem;
            outline: none;
        }

        .modal-body textarea.sdb-input {
            height: 70px;
            padding: 10px 12px;
            resize: vertical;
        }

        .modal-body .sdb-input:focus,
        .modal-body .sdb-select:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        [data-theme="dark"] .modal-body .sdb-input,
        [data-theme="dark"] .modal-body .sdb-select {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .10);
            color: #eef2f7;
        }

        [data-theme="dark"] .modal-body .sdb-input:focus,
        [data-theme="dark"] .modal-body .sdb-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91, 156, 246, .15);
        }

        [data-theme="dark"] .modal-body textarea.sdb-input {
            color: #eef2f7;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border: none;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: var(--accent-h);
        }

        .btn-primary:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            background: transparent;
            color: var(--t2);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-ghost:hover {
            background: var(--hover);
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid rgba(248, 113, 113, .3);
            border-radius: 10px;
            background: rgba(248, 113, 113, .08);
            color: var(--danger);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #fff;
            border-color: var(--danger);
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9100;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s, visibility .2s;
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
            padding: 24px;
            text-align: center;
            max-width: 360px;
            width: 100%;
        }

        [data-theme="dark"] .confirm-box {
            background: #0f1729;
            border-color: rgba(255, 255, 255, .08);
        }

        .confirm-box__icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
            font-size: 1.2rem;
            margin-bottom: 14px;
        }

        .confirm-box__msg {
            font-size: .88rem;
            color: var(--t2);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        [data-theme="dark"] .confirm-box__msg {
            color: #b0bdd0;
        }

        .confirm-box__actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .cal-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        .cal-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--card-border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: calSpin .6s linear infinite;
        }

        @keyframes calSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-spinner {
            display: inline-block;
            animation: calSpin .55s linear infinite;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .toolbar {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
                padding: 8px 10px;
            }

            .toolbar__sep {
                display: none;
            }

            .toolbar .sdb-input {
                flex: 1;
                max-width: 100%;
                min-width: 120px;
            }

            .area-filter {
                width: 100%;
                order: -1;
            }

            .area-filter__select {
                flex: 1;
                min-width: 0;
                max-width: 100%;
            }

            .filter-chip {
                padding: 5px 10px;
                font-size: .74rem;
            }

            .year-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .field-row {
                grid-template-columns: 1fr;
            }

            .period-bar {
                padding: 8px 10px;
                gap: 6px;
                font-size: .78rem;
            }

            .period-bar__label {
                font-size: .78rem;
            }

            .modal-overlay {
                padding: 12px;
                align-items: center;
                justify-content: center;
            }

            .modal-box {
                max-width: 100%;
                max-height: 88vh;
                border-radius: 16px;
                transform: translateY(20px) scale(.95);
            }

            .modal-overlay.is-open .modal-box {
                transform: translateY(0) scale(1);
            }

            .modal-body {
                padding: 16px;
            }

            .modal-foot {
                padding: 12px 16px;
            }

            .day-slot {
                padding: 8px 12px;
                gap: 8px;
            }

            .day-slot__hora {
                min-width: 48px;
                font-size: .8rem;
            }

            .week-grid {
                -webkit-overflow-scrolling: touch;
            }

            .week-grid table {
                min-width: 600px;
            }

            .week-grid th {
                padding: 8px 4px;
                font-size: .68rem;
                letter-spacing: .2px;
            }

            .week-grid th small {
                font-size: .6rem;
            }

            .week-grid th:first-child,
            .week-grid .td-hora {
                width: 56px;
                min-width: 56px;
                max-width: 56px;
                font-size: .72rem;
            }

            .week-grid td {
                padding: 3px 4px;
            }

            .rv {
                padding: 3px 5px;
                font-size: .7rem;
                gap: 4px;
                border-left-width: 2px;
            }

            .rv__name {
                font-size: .7rem;
            }

            .rv__area {
                font-size: .56rem;
            }

            .rv__date {
                font-size: .6rem;
            }

            .rv__obs {
                font-size: .58rem;
            }

            .rv__badge {
                font-size: .52rem;
                padding: 1px 4px;
            }

            .cell-add {
                font-size: .64rem;
                padding: 2px;
                margin-top: 2px;
            }
        }

        @media (max-width: 480px) {
            .year-grid {
                grid-template-columns: 1fr;
            }

            .area-filter__select {
                min-width: 120px;
                font-size: .78rem;
            }

            .view-modes {
                width: 100%;
            }

            .view-mode {
                flex: 1;
                text-align: center;
                padding: 6px 8px;
                font-size: .74rem;
            }

            .filter-chip {
                padding: 5px 8px;
                font-size: .72rem;
                gap: 3px;
            }

            .filter-chip i {
                font-size: .7rem;
            }

            .rv {
                padding: 2px 4px;
                font-size: .64rem;
                gap: 3px;
                border-radius: 5px;
            }

            .rv__name {
                font-size: .64rem;
            }

            .rv__area {
                font-size: .52rem;
            }

            .rv__badge {
                font-size: .48rem;
                padding: 1px 3px;
                border-radius: 3px;
            }

            .rv__obs {
                display: none;
            }

            .rv__date {
                font-size: .56rem;
            }

            .cell-add {
                font-size: .58rem;
                padding: 1px;
            }

            .cell-more {
                font-size: .6rem;
                padding: 2px 4px;
            }

            .period-bar__divider {
                display: none;
            }

            .period-bar__label {
                font-size: .74rem;
            }

            .period-bar__count {
                font-size: .7rem;
            }

            .month-grid td {
                padding: 4px;
                min-height: 60px;
                height: 60px;
            }

            .month-day {
                font-size: .72rem;
            }

            .month-count {
                font-size: .62rem;
                padding: 1px 4px;
            }

            .modal-overlay {
                padding: 8px;
            }

            .modal-box {
                max-height: 90vh;
            }

            .modal-head {
                padding: 12px 14px;
            }

            .modal-head__title {
                font-size: .88rem;
            }

            .modal-body {
                padding: 12px 14px;
            }

            .modal-body .sdb-input,
            .modal-body .sdb-select {
                height: 38px;
                font-size: .8rem;
            }

            .sdb-label {
                font-size: .74rem;
                margin-bottom: 4px;
            }

            .field {
                margin-bottom: 10px;
            }

            .modal-foot {
                padding: 10px 14px;
            }

            .btn-primary,
            .btn-ghost,
            .btn-danger {
                font-size: .78rem;
                padding: 7px 12px;
            }
        }

        @supports (padding: env(safe-area-inset-bottom)) {
            @media (max-width: 480px) {
                .modal-foot {
                    padding-bottom: max(10px, env(safe-area-inset-bottom));
                }
            }
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 9px !important;
            }

            .topnav,
            .bottomnav,
            .area-filter,
            .toolbar,
            .period-bar,
            .cal-loading,
            .cell-add,
            .cell-more,
            .act-btn,
            .modal-overlay,
            .confirm-overlay,
            .page-header__sub,
            #btnExportPdf,
            #btnExportXlsx {
                display: none !important;
            }

            .cell-overflow {
                display: block !important;
            }

            .page {
                padding-top: 0 !important;
            }

            .page-header {
                padding: 0 !important;
                margin-bottom: 8px !important;
            }

            .page-header__title {
                font-size: 14px !important;
                color: #000 !important;
            }

            .page-content {
                padding: 0 !important;
            }

            .week-grid {
                border: 1px solid #999 !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .week-grid table {
                min-width: 0 !important;
            }

            .week-grid th {
                background: #e5e7eb !important;
                color: #000 !important;
                font-size: 8px !important;
                padding: 4px 3px !important;
                position: static !important;
            }

            .week-grid td {
                font-size: 7px !important;
                padding: 2px 3px !important;
                background: #fff !important;
            }

            .week-grid .td-hora {
                position: static !important;
                background: #f3f4f6 !important;
                color: #000 !important;
                font-size: 8px !important;
                border-right-width: 1px !important;
            }

            .rv {
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 7px !important;
                margin-bottom: 2px !important;
                gap: 3px !important;
                page-break-inside: avoid;
            }

            .rv--fixa {
                background: #dbeafe !important;
                color: #000 !important;
                border-left: 2px solid #3b82f6 !important;
            }

            .rv--unica {
                background: #fef3c7 !important;
                color: #000 !important;
                border-left: 2px solid #f59e0b !important;
            }

            .rv__badge {
                font-size: 6px !important;
                padding: 1px 3px !important;
            }

            .rv__name {
                font-size: 7px !important;
            }

            .rv__obs,
            .rv__date,
            .rv__area {
                font-size: 6px !important;
            }

            .day-grid {
                border-radius: 0 !important;
            }

            .day-slot {
                padding: 4px 8px !important;
            }

            .month-grid {
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .month-grid th {
                background: #e5e7eb !important;
                color: #000 !important;
                position: static !important;
            }

            .month-grid td {
                background: #fff !important;
            }

            .year-grid {
                grid-template-columns: repeat(4, 1fr) !important;
            }

            .year-month {
                border-radius: 0 !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="toolbar">
        <div class="area-filter">
            <label class="area-filter__label" for="filtroArea"><i class="fi fi-rr-marker"></i></label>
            <select class="area-filter__select" id="filtroArea" onchange="selecionarArea(this.value)">
                <option value="">Todas as Áreas</option>
                @foreach ($areasPorTipo as $grupo)
                    <optgroup label="{{ $grupo['tipo']->nome }}">
                        @foreach ($grupo['areas'] as $a)
                            <option value="{{ $a->id }}"
                                {{ $areaAtiva && $areaAtiva->id === $a->id ? 'selected' : '' }}>
                                {{ $a->nome }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="toolbar__sep"></div>
        <div class="view-modes">
            <button type="button" class="view-mode active" data-view="week"
                onclick="trocarView('week')">Semana</button>
            <button type="button" class="view-mode" data-view="day" onclick="trocarView('day')">Dia</button>
            <button type="button" class="view-mode" data-view="month" onclick="trocarView('month')">Mês</button>
            <button type="button" class="view-mode" data-view="year" onclick="trocarView('year')">Ano</button>
        </div>
        <div class="toolbar__sep"></div>
        <button type="button" class="filter-chip active" id="chipTodos" onclick="filtrarTipo(null)"><i
                class="fi fi-rr-list"></i> Todos</button>
        <button type="button" class="filter-chip" id="chipFixa" onclick="filtrarTipo('FIXA')"><i
                class="fi fi-rr-refresh"></i> Fixas</button>
        <button type="button" class="filter-chip" id="chipUnica" onclick="filtrarTipo('UNICA')"><i
                class="fi fi-rr-calendar-day"></i> Únicas</button>
        <button type="button" class="filter-chip" id="chipMensalista" onclick="filtrarTipo('MENSALISTA')"><i
                class="fi fi-rr-user"></i> Mensalistas</button>
        <div class="toolbar__sep"></div>
        <input type="text" class="sdb-input" id="filtroCliente" placeholder="Buscar cliente ou obs..."
            oninput="debounceBusca()">
    </div>

    <div class="period-bar" id="periodBar" style="display:none;"></div>

    <div class="cal-loading" id="calLoading">
        <div class="cal-spinner"></div>
    </div>
    <div id="calContainer"></div>

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
                            placeholder="Buscar cliente por nome..." autocomplete="off" oninput="buscarCliente()">
                        <div class="cli-results" id="cliResults"></div>
                    </div>
                    <div class="cli-selected" id="cliSelected" style="display:none">
                        <span id="cliSelectedNome"></span>
                        <button type="button" class="cli-selected__clear" onclick="limparCliente()"><i
                                class="fi fi-rr-cross-small"></i></button>
                    </div>
                </div>
                <div class="field">
                    <label class="sdb-label">Tipo <span class="req">*</span></label>
                    <select id="reserva-tipo" class="sdb-select" onchange="onTipoChange()">
                        <option value="UNICA">Única</option>
                        <option value="FIXA">Fixa</option>
                        <option value="MENSALISTA">Mensalista</option>
                    </select>
                </div>
                <div class="field" id="fieldDataReserva">
                    <label class="sdb-label">Data da Reserva</label>
                    <input type="date" id="reserva-data" class="sdb-input">
                </div>
                <div class="field" id="fieldPeriodoFixa" style="display:none">
                    <label class="sdb-label">Período da Reserva Fixa</label>
                    <div class="field-row">
                        <div>
                            <label class="sdb-label">Início</label>
                            <input type="date" id="reserva-data-inicio" class="sdb-input">
                        </div>
                        <div>
                            <label class="sdb-label">Fim (opcional)</label>
                            <input type="date" id="reserva-data-fim" class="sdb-input">
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="sdb-label">Observações</label>
                    <textarea id="reserva-obs" class="sdb-input" placeholder="Anotações opcionais..."></textarea>
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

            var state = {
                areaId: {{ $areaAtiva->id ?? 'null' }},
                area: {{ Js::from($areaJson) }},
                reservas: {{ Js::from($reservasIniciais) }},
                todasAreas: {{ Js::from($todasAreasJson) }},
                horariosUnificados: {{ Js::from($horariosUnificadosJson) }},
                view: 'week',
                filtroTipo: null,
                filtroCliente: '',
                diaAtual: 0,
                mesAtual: new Date().getMonth(),
                anoAtual: new Date().getFullYear(),
                semanaOffset: 0,
                dataRefDia: null,
                loading: false
            };

            var reservaMap = {};
            var reservaIndex = {};
            var reservaByDate = {};
            var canCriar = {{ auth()->user()->can('reservas.criar') ? 'true' : 'false' }};
            var canEditar = {{ auth()->user()->can('reservas.editar') ? 'true' : 'false' }};
            var canExcluir = {{ auth()->user()->can('reservas.excluir') ? 'true' : 'false' }};
            var confirmCallback = null;
            var debounceTimer = null;
            var currentAbort = null;
            var _escDiv = document.createElement('div');
            var MAX_CHIPS_CELL = 3;

            var ROUTE_STORE = '{{ route('reservas.store') }}';
            var ROUTE_UPDATE = '{{ route('reservas.update', '__ID__') }}';
            var ROUTE_DESTROY = '{{ route('reservas.destroy', '__ID__') }}';
            var ROUTE_DATA = '{{ route('reservas.data') }}';
            var ROUTE_EXPORT_PDF = '{{ route('reservas.exportar.pdf.filtrado') }}';
            var ROUTE_EXPORT_XLSX = '{{ route('reservas.exportar.xlsx.filtrado') }}';

            var $pageTitle = document.getElementById('pageTitle');
            var $pageSub = document.getElementById('pageSub');
            var $calContainer = document.getElementById('calContainer');
            var $periodBar = document.getElementById('periodBar');
            var $loadingEl = document.getElementById('calLoading');
            var $btnPdf = document.getElementById('btnExportPdf');
            var $btnXlsx = document.getElementById('btnExportXlsx');
            var $filtroCliente = document.getElementById('filtroCliente');

            var $modalOverlay = document.getElementById('modalReserva');
            var $modalTitulo = document.getElementById('modalTitulo');
            var $rId = document.getElementById('reserva-id');
            var $rDia = document.getElementById('reserva-dia');
            var $rHorario = document.getElementById('reserva-horario');
            var $rClienteId = document.getElementById('reserva-cliente-id');
            var $rClienteBusca = document.getElementById('reserva-cliente-busca');
            var $cliResults = document.getElementById('cliResults');
            var $cliSelected = document.getElementById('cliSelected');
            var $cliSelectedNome = document.getElementById('cliSelectedNome');
            var $rTipo = document.getElementById('reserva-tipo');
            var $rData = document.getElementById('reserva-data');
            var $rDataInicio = document.getElementById('reserva-data-inicio');
            var $rDataFim = document.getElementById('reserva-data-fim');
            var $rObs = document.getElementById('reserva-obs');
            var $fieldData = document.getElementById('fieldDataReserva');
            var $fieldFixa = document.getElementById('fieldPeriodoFixa');
            var $fieldArea = document.getElementById('fieldArea');
            var $rArea = document.getElementById('reserva-area');
            var $btnExcluir = document.getElementById('btnExcluir');
            var $btnSalvar = document.getElementById('btnSalvar');
            var $confirmOverlay = document.getElementById('confirmOverlay');
            var $confirmMsg = document.getElementById('confirmMsg');
            var $viewModes = document.querySelectorAll('.view-mode');
            var $chipTodos = document.getElementById('chipTodos');
            var $chipFixa = document.getElementById('chipFixa');
            var $chipUnica = document.getElementById('chipUnica');
            var $chipMensalista = document.getElementById('chipMensalista');
            var ROUTE_BUSCAR_CLIENTE = '{{ route('clientes.buscar') }}';
            var cliSearchTimer = null;

            var DIAS_PT = {
                DOMINGO: 'Domingo',
                SEGUNDA: 'Segunda',
                TERCA: 'Terça',
                QUARTA: 'Quarta',
                QUINTA: 'Quinta',
                SEXTA: 'Sexta',
                SABADO: 'Sábado'
            };
            var DIAS_SEMANA_ISO = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];
            var MESES_PT = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
                'Outubro', 'Novembro', 'Dezembro'
            ];

            function getGridDias() {
                return state.area ? state.area.dias : DIAS_SEMANA_ISO;
            }

            function getGridHorarios() {
                return state.area ? state.area.horarios : state.horariosUnificados;
            }

            function hojeKey() {
                return DIAS_SEMANA_ISO[(new Date().getDay() + 6) % 7];
            }

            function fmtIso(d) {
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate())
                    .padStart(2, '0');
            }

            function fmtLong(d) {
                return d.getDate() + ' de ' + MESES_PT[d.getMonth()] + ' de ' + d.getFullYear();
            }

            function sameDate(a, b) {
                return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b
                    .getDate();
            }

            function escHtml(str) {
                if (!str) return '';
                _escDiv.textContent = str;
                return _escDiv.innerHTML;
            }

            function indexReservas() {
                reservaMap = {};
                reservaIndex = {};
                reservaByDate = {};
                if (!state.reservas) return;
                for (var i = 0; i < state.reservas.length; i++) {
                    var r = state.reservas[i];
                    reservaMap[r.id] = r;
                    var key = r.dia_semana + '|' + r.horario_inicio;
                    if (!reservaIndex[key]) reservaIndex[key] = [];
                    reservaIndex[key].push(r);
                    if (r.tipo === 'UNICA' && r.data_reserva) {
                        if (!reservaByDate[r.data_reserva]) reservaByDate[r.data_reserva] = [];
                        reservaByDate[r.data_reserva].push(r);
                    }
                }
            }

            function getWeekDates() {
                var hoje = new Date();
                hoje.setDate(hoje.getDate() + (state.semanaOffset * 7));
                var dow = (hoje.getDay() + 6) % 7;
                var seg = new Date(hoje);
                seg.setDate(seg.getDate() - dow);
                var map = {};
                for (var i = 0; i < 7; i++) {
                    var d = new Date(seg);
                    d.setDate(seg.getDate() + i);
                    map[DIAS_SEMANA_ISO[i]] = d;
                }
                return map;
            }

            function fixaVisivelNaData(r, cellDate) {
                if (!cellDate) return true;
                var cellIso = fmtIso(cellDate);
                if (r.data_inicio && cellIso < r.data_inicio) return false;
                if (r.data_fim && cellIso > r.data_fim) return false;
                return true;
            }

            function filterForCell(reservas, diaKey, hora, cellDate) {
                var key = diaKey + '|' + hora;
                var bucket = reservaIndex[key];
                if (!bucket || !bucket.length) return [];
                var results = [];
                for (var i = 0; i < bucket.length; i++) {
                    var r = bucket[i];
                    if (r.tipo === 'FIXA' || r.tipo === 'MENSALISTA') {
                        if (fixaVisivelNaData(r, cellDate)) results.push(r);
                    } else if (r.tipo === 'UNICA') {
                        if (cellDate && r.data_reserva && sameDate(new Date(r.data_reserva + 'T00:00:00'), cellDate))
                            results.push(r);
                    }
                }
                return results;
            }

            function filterForMonthDay(reservas, diaKey, ano, mes, dia) {
                var cellDate = new Date(ano, mes, dia);
                var cellIso = fmtIso(cellDate);
                var results = [];
                for (var key in reservaIndex) {
                    if (key.indexOf(diaKey + '|') !== 0) continue;
                    var bucket = reservaIndex[key];
                    for (var i = 0; i < bucket.length; i++) {
                        if ((bucket[i].tipo === 'FIXA' || bucket[i].tipo === 'MENSALISTA') && fixaVisivelNaData(bucket[
                                i], cellDate))
                            results.push(bucket[i]);
                    }
                }
                var dateBucket = reservaByDate[cellIso];
                if (dateBucket) {
                    for (var j = 0; j < dateBucket.length; j++) {
                        results.push(dateBucket[j]);
                    }
                }
                return results;
            }

            function countForMonth(reservas, ano, mes) {
                if (!reservas || !reservas.length) return {
                    fixas: 0,
                    unicas: 0
                };
                var ultimoDia = new Date(ano, mes + 1, 0).getDate();
                var fixas = 0,
                    unicas = 0;
                for (var d = 1; d <= ultimoDia; d++) {
                    var iso = ano + '-' + String(mes + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                    var dateBucket = reservaByDate[iso];
                    if (dateBucket) unicas += dateBucket.length;
                }
                var fixaCounted = {};
                for (var key in reservaIndex) {
                    var bucket = reservaIndex[key];
                    for (var i = 0; i < bucket.length; i++) {
                        var r = bucket[i];
                        if (r.tipo !== 'FIXA' && r.tipo !== 'MENSALISTA') continue;
                        if (fixaCounted[r.id]) continue;
                        for (var d2 = 1; d2 <= ultimoDia; d2++) {
                            var dKey = DIAS_SEMANA_ISO[(new Date(ano, mes, d2).getDay() + 6) % 7];
                            if (dKey !== r.dia_semana) continue;
                            var cellDate = new Date(ano, mes, d2);
                            if (fixaVisivelNaData(r, cellDate)) {
                                fixas++;
                                fixaCounted[r.id] = true;
                                break;
                            }
                        }
                    }
                }
                return {
                    fixas: fixas,
                    unicas: unicas
                };
            }

            function showLoading(show) {
                if ($loadingEl) $loadingEl.style.display = show ? 'flex' : 'none';
                if ($calContainer) $calContainer.style.display = show ? 'none' : 'block';
            }

            function setCalHtml(html) {
                requestAnimationFrame(function() {
                    $calContainer.innerHTML = html;
                });
            }

            function renderChip(r) {
                var cls = 'rv rv--' + r.tipo.toLowerCase();
                var dataAttr = canEditar ? ' data-rv-id="' + r.id + '"' : '';
                var h = '<div class="' + cls + '"' + dataAttr + '><div class="rv__info">';
                if (!state.area && r.area_nome) h += '<div class="rv__area">' + escHtml(r.area_nome) +
                    '</div>';
                h += '<div class="rv__name">' + escHtml(r.cliente_nome) + '</div>';
                if (r.horario_inicio && r.horario_fim && r.slots_ocupados > 1) {
                    h += '<div class="rv__date">' + r.horario_inicio + ' - ' + r.horario_fim + '</div>';
                }
                if (r.tipo === 'UNICA' && r.data_formatada) h += '<div class="rv__date">' + r.data_formatada + '</div>';
                if ((r.tipo === 'FIXA' || r.tipo === 'MENSALISTA') && (r.data_inicio || r.data_fim)) {
                    var p = r.data_inicio ? fmtBr(r.data_inicio) : '';
                    p += ' → ';
                    p += r.data_fim ? fmtBr(r.data_fim) : '∞';
                    h += '<div class="rv__date">' + p + '</div>';
                }
                if (r.valor_final) h += '<div class="rv__obs">R$ ' + parseFloat(r.valor_final).toFixed(2).replace('.',
                    ',') + '</div>';
                if (r.obs) h += '<div class="rv__obs">' + escHtml(r.obs) + '</div>';
                h += '</div><span class="rv__badge">' + r.tipo + '</span></div>';
                return h;
            }

            function fmtBr(iso) {
                if (!iso) return '';
                var p = iso.split('-');
                return p[2] + '/' + p[1] + '/' + p[0];
            }

            function renderCellChips(items, cellId) {
                if (!items.length) return '';
                var h = '';
                var visible = items.slice(0, MAX_CHIPS_CELL);
                var hidden = items.slice(MAX_CHIPS_CELL);
                for (var i = 0; i < visible.length; i++) h += renderChip(visible[i]);
                if (hidden.length) {
                    h += '<div class="cell-overflow" id="overflow-' + cellId + '" style="display:none">';
                    for (var j = 0; j < hidden.length; j++) h += renderChip(hidden[j]);
                    h += '</div><button type="button" class="cell-more" data-more="' + cellId +
                        '">+' + hidden.length + ' reserva' + (hidden.length > 1 ? 's' : '') + '</button>';
                }
                return h;
            }

            function renderWeek(reservas) {
                var hoje = new Date();
                var weekDates = getWeekDates();
                var dias = getGridDias();
                var horarios = getGridHorarios();
                if (!horarios.length) {
                    setCalHtml(
                        '<div class="empty-state"><i class="fi fi-rr-calendar-xmark"></i><div class="empty-state__text">Nenhum horário disponível</div></div>'
                    );
                    return;
                }
                var h = '<div class="week-grid"><table><thead><tr><th>Horário</th>';
                for (var di = 0; di < dias.length; di++) {
                    var dia = dias[di],
                        dt = weekDates[dia],
                        isHoje = dt && sameDate(dt, hoje);
                    h += '<th' + (isHoje ? ' class="th-today"' : '') + '>' + (DIAS_PT[dia] || dia);
                    if (dt) h += '<small>' + dt.getDate() + '/' + (dt.getMonth() + 1) + (isHoje ? ' •' : '') +
                        '</small>';
                    h += '</th>';
                }
                h += '</tr></thead><tbody>';
                var ci = 0;
                for (var hi = 0; hi < horarios.length; hi++) {
                    var hora = horarios[hi];
                    h += '<tr><td class="td-hora">' + hora + '</td>';
                    for (var dj = 0; dj < dias.length; dj++) {
                        var dia2 = dias[dj],
                            dt2 = weekDates[dia2];
                        var celula = filterForCell(reservas, dia2, hora, dt2);
                        var cid = 'w' + ci++;
                        h += '<td>' + renderCellChips(celula, cid);
                        if (canCriar) {
                            var dtIso = dt2 ? fmtIso(dt2) : '';
                            h += '<button type="button" class="cell-add" data-add data-add-dia="' + dia2 +
                                '" data-add-hora="' +
                                hora + '" data-add-data="' + dtIso + '"><i class="fi fi-rr-plus-small"></i></button>';
                        }
                        h += '</td>';
                    }
                    h += '</tr>';
                }
                h += '</tbody></table></div>';
                setCalHtml(h);
            }

            function renderDay(reservas) {
                var dias = getGridDias(),
                    horarios = getGridHorarios(),
                    diaDate, dia;
                if (state.dataRefDia) {
                    diaDate = new Date(state.dataRefDia + 'T12:00:00');
                    dia = DIAS_SEMANA_ISO[(diaDate.getDay() + 6) % 7];
                } else if (state.area) {
                    dia = dias[state.diaAtual];
                    diaDate = getWeekDates()[dia];
                } else {
                    diaDate = new Date();
                    dia = DIAS_SEMANA_ISO[(diaDate.getDay() + 6) % 7];
                }
                if (!horarios.length) {
                    setCalHtml(
                        '<div class="empty-state"><i class="fi fi-rr-calendar-xmark"></i><div class="empty-state__text">Nenhum horário disponível</div></div>'
                    );
                    return;
                }
                var h = '<div class="day-grid">';
                var ci = 0;
                for (var i = 0; i < horarios.length; i++) {
                    var hora = horarios[i];
                    var celula = filterForCell(reservas, dia, hora, diaDate);
                    var cid = 'd' + ci++;
                    h += '<div class="day-slot"><div class="day-slot__hora">' + hora +
                        '</div><div class="day-slot__body">';
                    if (!celula.length) h += '<div class="day-slot__empty">Livre</div>';
                    else h += renderCellChips(celula, cid);
                    if (canCriar) {
                        var dtIso = diaDate ? fmtIso(diaDate) : '';
                        h += '<button type="button" class="cell-add" data-add data-add-dia="' + dia +
                            '" data-add-hora="' +
                            hora + '" data-add-data="' + dtIso +
                            '"><i class="fi fi-rr-plus-small"></i> Reservar</button>';
                    }
                    h += '</div></div>';
                }
                h += '</div>';
                setCalHtml(h);
            }

            function renderMonth(reservas) {
                var hoje = new Date(),
                    ano = state.anoAtual,
                    mes = state.mesAtual;
                var primeiroDia = new Date(ano, mes, 1);
                var ultimoDia = new Date(ano, mes + 1, 0);
                var diaSemanaInicio = (primeiroDia.getDay() + 6) % 7;
                var h = '<div class="month-grid"><table><thead><tr>';
                var diasCurtos = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
                for (var i = 0; i < 7; i++) h += '<th>' + diasCurtos[i] + '</th>';
                h += '</tr></thead><tbody><tr>';
                for (var e = 0; e < diaSemanaInicio; e++) h += '<td class="month-cell--empty"></td>';
                for (var d = 1; d <= ultimoDia.getDate(); d++) {
                    var diaSemana = DIAS_SEMANA_ISO[(new Date(ano, mes, d).getDay() + 6) % 7];
                    var diaRes = filterForMonthDay(reservas, diaSemana, ano, mes, d);
                    var fixas = 0,
                        unicas = 0;
                    for (var k = 0; k < diaRes.length; k++) {
                        if (diaRes[k].tipo === 'FIXA') fixas++;
                        else unicas++;
                    }
                    var isHoje = d === hoje.getDate() && mes === hoje.getMonth() && ano === hoje.getFullYear();
                    h += '<td class="month-cell" data-goto-dia="' + d + '">';
                    h += '<div class="month-day' + (isHoje ? ' month-day--today' : '') + '">' + d + '</div>';
                    if (fixas) h += '<div class="month-count month-count--fixa">' + fixas + ' fixa' + (fixas > 1 ? 's' :
                        '') + '</div> ';
                    if (unicas) h += '<div class="month-count month-count--unica">' + unicas + ' única' + (unicas > 1 ?
                        's' : '') + '</div>';
                    h += '</td>';
                    if ((diaSemanaInicio + d) % 7 === 0 && d < ultimoDia.getDate()) h += '</tr><tr>';
                }
                var cellsUsed = diaSemanaInicio + ultimoDia.getDate();
                var remaining = (7 - (cellsUsed % 7)) % 7;
                for (var e2 = 0; e2 < remaining; e2++) h += '<td class="month-cell--empty"></td>';
                h += '</tr></tbody></table></div>';
                setCalHtml(h);
            }

            function renderYear(reservas) {
                var hoje = new Date(),
                    ano = state.anoAtual;
                var h = '<div class="year-grid">';
                for (var m = 0; m < 12; m++) {
                    var isCurrent = m === hoje.getMonth() && ano === hoje.getFullYear();
                    var primeiroDia = new Date(ano, m, 1);
                    var numDias = new Date(ano, m + 1, 0).getDate();
                    var diaSemanaInicio = (primeiroDia.getDay() + 6) % 7;
                    var counts = countForMonth(reservas, ano, m);
                    var total = counts.fixas + counts.unicas;
                    var diasOcupados = {};
                    for (var d = 1; d <= numDias; d++) {
                        var dKey = DIAS_SEMANA_ISO[(new Date(ano, m, d).getDay() + 6) % 7];
                        if (filterForMonthDay(reservas, dKey, ano, m, d).length) diasOcupados[d] = true;
                    }
                    h += '<div class="year-month' + (isCurrent ? ' year-month--current' : '') +
                        '" data-goto-mes="' + m + '">';
                    h += '<div class="year-month__head"><span>' + MESES_PT[m] + '</span>';
                    h += '<span class="year-month__count"' + (total ? '' : ' style="opacity:.4"') + '>' + (total ||
                        '—') + '</span></div>';
                    h += '<div class="year-month__body"><div class="year-mini-grid">';
                    var hdrLetters = ['S', 'T', 'Q', 'Q', 'S', 'S', 'D'];
                    for (var hl = 0; hl < 7; hl++) h += '<div class="year-mini-grid__hdr">' + hdrLetters[hl] + '</div>';
                    for (var e = 0; e < diaSemanaInicio; e++) h +=
                        '<div class="year-mini-day year-mini-day--empty">.</div>';
                    for (var d2 = 1; d2 <= numDias; d2++) {
                        var isHoje = d2 === hoje.getDate() && m === hoje.getMonth() && ano === hoje.getFullYear();
                        var cls = 'year-mini-day';
                        if (isHoje) cls += ' year-mini-day--today';
                        else if (diasOcupados[d2]) cls += ' year-mini-day--has';
                        h += '<div class="' + cls + '">' + d2 + '</div>';
                    }
                    h += '</div></div>';
                    if (counts.fixas || counts.unicas) {
                        h += '<div class="year-month__stats">';
                        if (counts.fixas) h += '<span class="year-month__stat year-month__stat--fixa">' + counts.fixas +
                            ' fixa</span>';
                        if (counts.unicas) h += '<span class="year-month__stat year-month__stat--unica">' + counts
                            .unicas + ' única' + (counts.unicas > 1 ? 's' : '') + '</span>';
                        h += '</div>';
                    }
                    h += '</div>';
                }
                h += '</div>';
                setCalHtml(h);
            }

            function renderView() {
                $pageTitle.textContent = state.area ? state.area.nome : 'Todas as Áreas';
                var r = state.reservas || [];
                switch (state.view) {
                    case 'week':
                        renderWeek(r);
                        break;
                    case 'day':
                        renderDay(r);
                        break;
                    case 'month':
                        renderMonth(r);
                        break;
                    case 'year':
                        renderYear(r);
                        break;
                }
            }

            function renderPeriodBar() {
                var bar = $periodBar;
                if (!bar) return;
                bar.style.display = 'flex';
                var hoje = new Date();
                var totalReservas = state.reservas ? state.reservas.length : 0;
                var h = '';
                if (state.view === 'week') {
                    var wDates = getWeekDates();
                    var seg = wDates['SEGUNDA'],
                        dom = wDates['DOMINGO'];
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navWeek(-1)"><i class="fi fi-rr-angle-left"></i></button>';
                    h += '<span class="period-bar__label">' + seg.getDate() + ' ' + MESES_PT[seg.getMonth()] + ' — ' +
                        dom.getDate() + ' ' + MESES_PT[dom.getMonth()] + ' ' + dom.getFullYear() + '</span>';
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navWeek(1)"><i class="fi fi-rr-angle-right"></i></button>';
                    h += '<div class="period-bar__divider"></div>';
                    h += '<span class="period-bar__count">' + totalReservas + ' reserva' + (totalReservas !== 1 ? 's' :
                        '') + '</span>';
                    if (state.semanaOffset !== 0) {
                        h += '<div class="period-bar__divider"></div>';
                        h += '<button type="button" class="period-bar__today" onclick="voltarHoje()">Hoje</button>';
                    }
                } else if (state.view === 'day') {
                    var diaDate, diaKey;
                    if (state.dataRefDia) {
                        diaDate = new Date(state.dataRefDia + 'T12:00:00');
                        diaKey = DIAS_SEMANA_ISO[(diaDate.getDay() + 6) % 7];
                    } else if (state.area) {
                        diaKey = getGridDias()[state.diaAtual];
                        diaDate = getWeekDates()[diaKey];
                    } else {
                        diaDate = new Date();
                        diaKey = DIAS_SEMANA_ISO[(diaDate.getDay() + 6) % 7];
                    }
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navDay(-1)"><i class="fi fi-rr-angle-left"></i></button>';
                    h += '<span class="period-bar__label">' + (DIAS_PT[diaKey] || diaKey);
                    if (diaDate) h += ', ' + diaDate.getDate() + ' de ' + MESES_PT[diaDate.getMonth()] + ' ' + diaDate
                        .getFullYear();
                    h += '</span>';
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navDay(1)"><i class="fi fi-rr-angle-right"></i></button>';
                    h += '<div class="period-bar__divider"></div>';
                    h += '<span class="period-bar__count">' + totalReservas + ' reserva' + (totalReservas !== 1 ? 's' :
                        '') + '</span>';
                    h += '<div class="period-bar__divider"></div>';
                    h += '<button type="button" class="period-bar__today" onclick="voltarHoje()">Hoje</button>';
                } else if (state.view === 'month') {
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navMonth(-1)"><i class="fi fi-rr-angle-left"></i></button>';
                    h += '<span class="period-bar__label">' + MESES_PT[state.mesAtual] + ' ' + state.anoAtual +
                        '</span>';
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navMonth(1)"><i class="fi fi-rr-angle-right"></i></button>';
                    h += '<div class="period-bar__divider"></div>';
                    h += '<span class="period-bar__count">' + totalReservas + ' reserva' + (totalReservas !== 1 ? 's' :
                        '') + '</span>';
                } else if (state.view === 'year') {
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navYear(-1)"><i class="fi fi-rr-angle-left"></i></button>';
                    h += '<span class="period-bar__label">' + state.anoAtual + '</span>';
                    h +=
                        '<button type="button" class="period-bar__btn" onclick="navYear(1)"><i class="fi fi-rr-angle-right"></i></button>';
                    h += '<div class="period-bar__divider"></div>';
                    h += '<span class="period-bar__count">' + totalReservas + ' reserva' + (totalReservas !== 1 ? 's' :
                        '') + '</span>';
                }
                bar.innerHTML = h;
            }

            function updatePageSub() {
                var hoje = new Date();
                var total = state.reservas ? state.reservas.length : 0;
                $pageSub.textContent = DIAS_PT[hojeKey()] + ', ' + fmtLong(hoje) + ' — ' + total + ' reserva' + (
                    total !== 1 ? 's' : '') + ' carregadas';
            }

            function updateExportLinks() {
                var params = new URLSearchParams();
                if (state.areaId) params.set('area_id', state.areaId);
                if (state.filtroTipo) params.set('tipo', state.filtroTipo);
                if (state.filtroCliente) params.set('busca', state.filtroCliente);
                var qs = params.toString();
                if ($btnPdf) $btnPdf.href = ROUTE_EXPORT_PDF + (qs ? '?' + qs : '');
                if ($btnXlsx) $btnXlsx.href = ROUTE_EXPORT_XLSX + (qs ? '?' + qs : '');
            }

            function getDataRef() {
                switch (state.view) {
                    case 'day':
                        if (state.dataRefDia) return state.dataRefDia;
                        var dias = state.area ? state.area.dias : DIAS_SEMANA_ISO;
                        var dk = dias[state.diaAtual] || dias[0];
                        var wd = getWeekDates();
                        return wd[dk] ? fmtIso(wd[dk]) : fmtIso(new Date());
                    case 'week':
                        var d = new Date();
                        d.setDate(d.getDate() + (state.semanaOffset * 7));
                        return fmtIso(d);
                    case 'month':
                        return fmtIso(new Date(state.anoAtual, state.mesAtual, 1));
                    case 'year':
                        return fmtIso(new Date(state.anoAtual, 0, 1));
                    default:
                        return fmtIso(new Date());
                }
            }

            function fetchApi(url, options) {
                options = options || {};
                var token = document.querySelector('meta[name="csrf-token"]');
                options.headers = Object.assign({
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                    'Accept': 'application/json'
                }, options.headers || {});
                return fetch(url, options).then(function(res) {
                    if (!res.ok) throw res;
                    return res.json();
                });
            }

            function carregarDados() {
                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();
                state.loading = true;
                showLoading(true);
                var params = new URLSearchParams();
                if (state.areaId) params.set('area_id', state.areaId);
                params.set('view', state.view);
                params.set('data_ref', getDataRef());
                if (state.filtroTipo) params.set('tipo', state.filtroTipo);
                if (state.filtroCliente) params.set('busca', state.filtroCliente);
                fetchApi(ROUTE_DATA + '?' + params.toString(), {
                        signal: currentAbort.signal
                    })
                    .then(function(data) {
                        if (data.area) state.area = data.area;
                        state.reservas = data.reservas || [];
                        indexReservas();
                        renderPeriodBar();
                        renderView();
                        updatePageSub();
                        updateExportLinks();
                    })
                    .catch(function(err) {
                        if (err.name !== 'AbortError') SdbToast.error('Erro ao carregar dados');
                    })
                    .finally(function() {
                        state.loading = false;
                        showLoading(false);
                    });
            }

            window.selecionarArea = function(val) {
                state.areaId = val ? parseInt(val) : null;
                state.area = null;
                state.diaAtual = 0;
                state.semanaOffset = 0;
                $pageTitle.textContent = state.areaId ? '' : 'Todas as Áreas';
                updateExportLinks();
                carregarDados();
            };

            window.trocarView = function(view) {
                state.view = view;
                if (view !== 'day') state.dataRefDia = null;
                else if (!state.area && !state.dataRefDia) state.dataRefDia = fmtIso(new Date());
                if (view !== 'week' && view !== 'day') state.semanaOffset = 0;
                $viewModes.forEach(function(b) {
                    b.classList.toggle('active', b.dataset.view === view);
                });
                carregarDados();
            };

            window.filtrarTipo = function(tipo) {
                state.filtroTipo = tipo;
                $chipTodos.classList.toggle('active', !tipo);
                $chipFixa.classList.toggle('active', tipo === 'FIXA');
                $chipUnica.classList.toggle('active', tipo === 'UNICA');
                $chipMensalista.classList.toggle('active', tipo === 'MENSALISTA');
                carregarDados();
            };

            window.debounceBusca = function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    state.filtroCliente = $filtroCliente.value.trim();
                    carregarDados();
                }, 400);
            };

            window.navDay = function(dir) {
                if (state.dataRefDia) {
                    var dt = new Date(state.dataRefDia + 'T12:00:00');
                    dt.setDate(dt.getDate() + dir);
                    state.dataRefDia = fmtIso(dt);
                    state.mesAtual = dt.getMonth();
                    state.anoAtual = dt.getFullYear();
                    carregarDados();
                    return;
                }
                if (state.area) {
                    var dias = state.area.dias;
                    state.diaAtual = (state.diaAtual + dir + dias.length) % dias.length;
                    carregarDados();
                    return;
                }
                state.dataRefDia = fmtIso(new Date());
                var dt2 = new Date(state.dataRefDia + 'T12:00:00');
                dt2.setDate(dt2.getDate() + dir);
                state.dataRefDia = fmtIso(dt2);
                state.mesAtual = dt2.getMonth();
                state.anoAtual = dt2.getFullYear();
                carregarDados();
            };

            window.navWeek = function(dir) {
                state.semanaOffset += dir;
                carregarDados();
            };

            window.navMonth = function(dir) {
                state.mesAtual += dir;
                if (state.mesAtual > 11) {
                    state.mesAtual = 0;
                    state.anoAtual++;
                }
                if (state.mesAtual < 0) {
                    state.mesAtual = 11;
                    state.anoAtual--;
                }
                carregarDados();
            };

            window.navYear = function(dir) {
                state.anoAtual += dir;
                carregarDados();
            };

            window.irParaMes = function(mes) {
                state.mesAtual = mes;
                state.view = 'month';
                state.dataRefDia = null;
                $viewModes.forEach(function(b) {
                    b.classList.toggle('active', b.dataset.view === 'month');
                });
                carregarDados();
            };

            window.irParaDia = function(dia) {
                var data = new Date(state.anoAtual, state.mesAtual, dia);
                state.dataRefDia = fmtIso(data);
                state.view = 'day';
                state.semanaOffset = Math.round((data.getTime() - new Date().getTime()) / (7 * 86400000));
                if (state.area) {
                    var diaKey = DIAS_SEMANA_ISO[(data.getDay() + 6) % 7];
                    var idx = state.area.dias.indexOf(diaKey);
                    if (idx >= 0) state.diaAtual = idx;
                }
                $viewModes.forEach(function(b) {
                    b.classList.toggle('active', b.dataset.view === 'day');
                });
                carregarDados();
            };

            window.voltarHoje = function() {
                var hoje = new Date();
                state.mesAtual = hoje.getMonth();
                state.anoAtual = hoje.getFullYear();
                state.semanaOffset = 0;
                state.dataRefDia = null;
                if (state.view === 'day' && state.area) {
                    var idx = state.area.dias.indexOf(hojeKey());
                    if (idx >= 0) state.diaAtual = idx;
                }
                carregarDados();
            };

            window.toggleOverflow = function(cellId, btn) {
                var el = document.getElementById('overflow-' + cellId);
                if (!el) return;
                var showing = el.style.display === 'none';
                el.style.display = showing ? 'block' : 'none';
                var total = el.children.length;
                btn.textContent = showing ? '− recolher' : ('+' + total + ' reserva' + (total > 1 ? 's' : ''));
            };

            window.buscarCliente = function() {
                var termo = $rClienteBusca.value.trim();
                if (cliSearchTimer) clearTimeout(cliSearchTimer);
                if (termo.length < 2) {
                    $cliResults.classList.remove('is-open');
                    return;
                }
                cliSearchTimer = setTimeout(function() {
                    fetch(ROUTE_BUSCAR_CLIENTE + '?termo=' + encodeURIComponent(termo), {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(clientes) {
                            if (!clientes.length) {
                                $cliResults.innerHTML =
                                    '<div class="cli-result-item" style="color:var(--t4)">Nenhum cliente encontrado</div>';
                            } else {
                                var h = '';
                                for (var i = 0; i < clientes.length; i++) {
                                    h += '<div class="cli-result-item" onclick="selecionarCliente(' +
                                        clientes[i].id + ',\'' + escHtml(clientes[i].nome).replace(/'/g,
                                            "\\'") + '\',\'' + escHtml(clientes[i].telefone || '')
                                        .replace(/'/g, "\\'") + '\')">';
                                    h += escHtml(clientes[i].nome);
                                    if (clientes[i].telefone) h +=
                                        '<span class="cli-result-item__tel">' + escHtml(clientes[i]
                                            .telefone) + '</span>';
                                    h += '</div>';
                                }
                                $cliResults.innerHTML = h;
                            }
                            $cliResults.classList.add('is-open');
                        })
                        .catch(function() {
                            $cliResults.classList.remove('is-open');
                        });
                }, 300);
            };

            window.selecionarCliente = function(id, nome, telefone) {
                $rClienteId.value = id;
                $cliSelectedNome.textContent = nome + (telefone ? ' · ' + telefone : '');
                $cliSelected.style.display = 'flex';
                $rClienteBusca.parentElement.style.display = 'none';
                $cliResults.classList.remove('is-open');
            };

            window.limparCliente = function() {
                $rClienteId.value = '';
                $cliSelectedNome.textContent = '';
                $cliSelected.style.display = 'none';
                $rClienteBusca.parentElement.style.display = 'block';
                $rClienteBusca.value = '';
                $cliResults.classList.remove('is-open');
            };

            window.onTipoChange = function() {
                var tipo = $rTipo.value;
                $fieldData.style.display = tipo === 'UNICA' ? 'block' : 'none';
                $fieldFixa.style.display = (tipo === 'FIXA' || tipo === 'MENSALISTA') ? 'block' : 'none';
            };

            window.abrirModalNovo = function(dia, horario, dataIso) {
                if (!canCriar) return;
                var titulo = (DIAS_PT[dia] || dia) + ' — ' + horario;
                if (dataIso) {
                    var p = dataIso.split('-');
                    titulo += ' — ' + p[2] + '/' + p[1] + '/' + p[0];
                }
                $modalTitulo.textContent = titulo;
                $rId.value = '';
                $rDia.value = dia;
                $rHorario.value = horario;
                limparCliente();
                $rClienteBusca.value = '';
                $rTipo.value = 'UNICA';
                $rData.value = dataIso || '';
                $rDataInicio.value = '';
                $rDataFim.value = '';
                $rObs.value = '';
                $fieldData.style.display = 'block';
                $fieldFixa.style.display = 'none';
                if (!state.area) {
                    $fieldArea.style.display = 'block';
                    $rArea.value = '';
                } else {
                    $fieldArea.style.display = 'none';
                    $rArea.value = state.areaId || '';
                }
                if ($btnExcluir) $btnExcluir.style.display = 'none';
                limparCliente();
                $rClienteBusca.value = '';
                $modalOverlay.classList.add('is-open');
                setTimeout(function() {
                    (state.area ? $rClienteBusca : $rArea).focus();
                }, 300);
            };

            window.abrirModalEditar = function(id) {
                var r = reservaMap[id];
                if (!r) return;
                $modalTitulo.textContent = 'Editar Reserva';
                $rId.value = r.id;
                $rDia.value = r.dia_semana;
                $rHorario.value = r.horario_inicio || '';
                $rClienteId.value = r.cliente_id || '';
                $rClienteBusca.value = '';
                if (r.cliente_id && r.cliente_nome) {
                    $cliSelectedNome.textContent = r.cliente_nome + (r.cliente_telefone ? ' · ' + r
                        .cliente_telefone : '');
                    $cliSelected.style.display = 'flex';
                    $rClienteBusca.parentElement.style.display = 'none';
                } else {
                    limparCliente();
                }
                $rTipo.value = r.tipo || 'UNICA';
                $rData.value = r.data_reserva || '';
                $rDataInicio.value = r.data_inicio || '';
                $rDataFim.value = r.data_fim || '';
                $rObs.value = r.obs || '';
                $fieldData.style.display = r.tipo === 'UNICA' ? 'block' : 'none';
                $fieldFixa.style.display = (r.tipo === 'FIXA' || r.tipo === 'MENSALISTA') ? 'block' : 'none';
                if (!state.area) {
                    $fieldArea.style.display = 'block';
                    $rArea.value = r.area_id || '';
                } else {
                    $fieldArea.style.display = 'none';
                    $rArea.value = r.area_id || state.areaId || '';
                }
                if ($btnExcluir) $btnExcluir.style.display = 'inline-flex';
                $modalOverlay.classList.add('is-open');
                setTimeout(function() {
                    $rClienteBusca.focus();
                }, 300);
            };

            window.fecharModal = function() {
                $modalOverlay.classList.remove('is-open');
            };

            window.salvarReserva = function() {
                if ($btnSalvar.disabled) return;
                var id = $rId.value;
                var clienteId = $rClienteId.value;
                if (!clienteId) {
                    SdbToast.error('Selecione um cliente');
                    return;
                }
                var areaId = state.areaId || $rArea.value;
                if (!areaId) {
                    SdbToast.error('Selecione a área');
                    return;
                }
                var tipo = $rTipo.value;
                if (tipo === 'UNICA' && !$rData.value) {
                    SdbToast.error('Informe a data da reserva');
                    return;
                }
                var dados = {
                    area_id: parseInt(areaId),
                    cliente_id: parseInt(clienteId),
                    dia_semana: $rDia.value,
                    horario_inicio: $rHorario.value || null,
                    tipo: tipo,
                    data_reserva: tipo === 'UNICA' ? ($rData.value || null) : null,
                    data_inicio: (tipo === 'FIXA' || tipo === 'MENSALISTA') ? ($rDataInicio.value || null) :
                        null,
                    data_fim: (tipo === 'FIXA' || tipo === 'MENSALISTA') ? ($rDataFim.value || null) : null,
                    obs: $rObs.value || null
                };
                $btnSalvar.disabled = true;
                $btnSalvar.innerHTML = '<span class="btn-spinner"></span>';
                var url = id ? ROUTE_UPDATE.replace('__ID__', id) : ROUTE_STORE;
                var method = id ? 'PUT' : 'POST';
                fetchApi(url, {
                        method: method,
                        body: JSON.stringify(dados)
                    })
                    .then(function() {
                        SdbToast.success(id ? 'Reserva atualizada' : 'Reserva criada');
                        fecharModal();
                        carregarDados();
                    })
                    .catch(function(err) {
                        if (err.status === 422) {
                            err.json().then(function(data) {
                                var msgs = Object.values(data.errors || {}).flat();
                                SdbToast.error(msgs.join(', ') || 'Dados inválidos');
                            });
                        } else if (err.status === 403) {
                            SdbToast.error('Sem permissão para esta ação');
                        } else if (err.status === 409) {
                            SdbToast.error('Conflito: já existe reserva neste horário');
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
                var id = $rId.value;
                if (!id) return;
                confirmar('Tem certeza que deseja excluir esta reserva?', function() {
                    if ($btnExcluir) {
                        $btnExcluir.disabled = true;
                        $btnExcluir.innerHTML = '<span class="btn-spinner"></span>';
                    }
                    fetchApi(ROUTE_DESTROY.replace('__ID__', id), {
                            method: 'DELETE'
                        })
                        .then(function() {
                            SdbToast.success('Reserva excluída');
                            fecharModal();
                            carregarDados();
                        })
                        .catch(function(err) {
                            if (err.status === 403) SdbToast.error('Sem permissão para excluir');
                            else SdbToast.error('Erro ao excluir reserva');
                        })
                        .finally(function() {
                            if ($btnExcluir) {
                                $btnExcluir.disabled = false;
                                $btnExcluir.innerHTML = '<i class="fi fi-rr-trash"></i> Excluir';
                            }
                        });
                });
            };

            function confirmar(msg, callback) {
                $confirmMsg.textContent = msg;
                confirmCallback = callback;
                $confirmOverlay.classList.add('is-open');
            }

            window.fecharConfirm = function(confirmed) {
                $confirmOverlay.classList.remove('is-open');
                if (confirmed && confirmCallback) confirmCallback();
                confirmCallback = null;
            };

            indexReservas();
            updateExportLinks();
            renderPeriodBar();
            renderView();
            updatePageSub();
            showLoading(false);

            $calContainer.addEventListener('click', function(e) {
                var target = e.target;
                var chip = target.closest('[data-rv-id]');
                if (chip) {
                    e.stopPropagation();
                    abrirModalEditar(parseInt(chip.dataset.rvId));
                    return;
                }
                var addBtn = target.closest('[data-add]');
                if (addBtn) {
                    var d = addBtn.dataset;
                    abrirModalNovo(d.addDia, d.addHora, d.addData || '');
                    return;
                }
                var moreBtn = target.closest('[data-more]');
                if (moreBtn) {
                    toggleOverflow(moreBtn.dataset.more, moreBtn);
                    return;
                }
                var monthCell = target.closest('[data-goto-dia]');
                if (monthCell) {
                    irParaDia(parseInt(monthCell.dataset.gotoDia));
                    return;
                }
                var yearMonth = target.closest('[data-goto-mes]');
                if (yearMonth) {
                    irParaMes(parseInt(yearMonth.dataset.gotoMes));
                    return;
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if ($confirmOverlay.classList.contains('is-open')) {
                        fecharConfirm(false);
                    } else if ($modalOverlay.classList.contains('is-open')) {
                        fecharModal();
                    }
                }
            });
        })();
    </script>
@endsection
