@extends('layouts.app')

@section('title', 'Histórico')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Histórico</h1>
            <p class="page-header__sub">Registro de todas as atividades do sistema</p>
        </div>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('historico.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}"
                class="act-btn-label">
                <i class="fi fi-rr-download"></i> Excel
            </a>
            <a href="{{ route('historico.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                class="act-btn-label">
                <i class="fi fi-rr-document"></i> PDF
            </a>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .act-btn-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
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
            transition: all .2s ease;
        }

        .act-btn-label:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
        }

        .act-btn-label i {
            font-size: .9rem;
        }

        /* ── Stats ── */

        .h-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .h-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: background .3s, border-color .3s;
        }

        .h-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .h-stat__ic--blue {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .h-stat__ic--green {
            background: rgba(52, 211, 153, .15);
            color: var(--success);
        }

        .h-stat__ic--amber {
            background: rgba(251, 191, 36, .15);
            color: var(--warning);
        }

        .h-stat__ic--purple {
            background: rgba(167, 139, 250, .15);
            color: var(--purple);
        }

        [data-theme="light"] .h-stat__ic--blue {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .h-stat__ic--green {
            background: rgba(16, 185, 129, .10);
        }

        [data-theme="light"] .h-stat__ic--amber {
            background: rgba(245, 158, 11, .10);
        }

        [data-theme="light"] .h-stat__ic--purple {
            background: rgba(139, 92, 246, .10);
        }

        .h-stat__val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
        }

        .h-stat__lbl {
            font-size: .78rem;
            font-weight: 500;
            color: var(--t3);
            margin-top: 2px;
        }

        /* ── Filters ── */

        .h-filters {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
            transition: background .35s ease, border-color .35s ease;
        }

        .h-chips {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .h-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 34px;
            padding: 0 14px;
            border-radius: 9px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            font-size: .8rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s ease;
            white-space: nowrap;
        }

        .h-chip:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
        }

        .h-chip.active {
            color: var(--accent);
            border-color: var(--accent);
            background: var(--active);
        }

        .h-chip i {
            font-size: .82rem;
        }

        .h-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1.5fr auto;
            gap: 10px;
            align-items: end;
        }

        .h-lbl {
            display: block;
            font-size: .72rem;
            font-weight: 600;
            color: var(--t3);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .h-input,
        .h-sel {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            color: var(--t1);
            font-family: inherit;
            font-size: .82rem;
            font-weight: 500;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .h-input::placeholder {
            color: var(--t4);
            font-weight: 400;
        }

        .h-input:hover,
        .h-sel:hover {
            border-color: var(--input-border-h);
        }

        .h-input:focus,
        .h-sel:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        .h-sel {
            padding-right: 34px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238a9bb5' d='M6 8.5L1.5 4h9z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        /* ── Dark mode: selects + date inputs ── */

        .h-sel,
        .h-input[type="date"] {
            color-scheme: dark;
        }

        [data-theme="light"] .h-sel,
        [data-theme="light"] .h-input[type="date"] {
            color-scheme: light;
        }

        [data-theme="dark"] .h-sel,
        [data-theme="dark"] .h-input,
        [data-theme="dark"] .h-input[type="date"] {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .10);
            color: #eef2f7;
        }

        [data-theme="dark"] .h-sel:focus,
        [data-theme="dark"] .h-input:focus,
        [data-theme="dark"] .h-input[type="date"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91, 156, 246, .15);
        }

        [data-theme="dark"] .h-input::placeholder {
            color: #4a5a74;
        }

        [data-theme="light"] .h-input::placeholder {
            color: #94a3b8;
        }

        [data-theme="dark"] .h-sel option {
            background: #1a2236;
            color: #eef2f7;
            padding: 8px 12px;
        }

        [data-theme="dark"] .h-sel option:checked {
            background: #2a3a56;
            color: #fff;
        }

        [data-theme="light"] .h-sel option {
            background: #ffffff;
            color: #0f172a;
            padding: 8px 12px;
        }

        [data-theme="light"] .h-sel option:checked {
            background: #e0edff;
            color: #1d4ed8;
        }

        /* ── Filter actions ── */

        .h-acts {
            display: flex;
            gap: 6px;
        }

        .h-btn-go {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: .88rem;
            cursor: pointer;
            transition: background .2s ease;
        }

        .h-btn-go:hover {
            background: var(--accent-h);
        }

        .h-btn-clr {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: transparent;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            color: var(--t3);
            font-size: .88rem;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s ease;
        }

        .h-btn-clr:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
        }

        /* ── Timeline day header ── */

        .tl-day {
            margin-bottom: 8px;
        }

        .tl-day__hdr {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0 8px;
        }

        .tl-day__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent);
            flex-shrink: 0;
        }

        .tl-day__lbl {
            font-size: .78rem;
            font-weight: 700;
            color: var(--t1);
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .tl-day__line {
            flex: 1;
            height: 1px;
            background: var(--card-border);
        }

        .tl-day__cnt {
            font-size: .7rem;
            font-weight: 600;
            color: var(--t4);
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--input-bg);
        }

        /* ── Audit card ── */

        .a-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            margin-bottom: 8px;
            overflow: hidden;
            transition: background .3s, border-color .3s;
        }

        .a-card:hover {
            border-color: var(--input-border-h);
        }

        .a-main {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            cursor: pointer;
            user-select: none;
        }

        .a-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .68rem;
            color: #fff;
            flex-shrink: 0;
            background: var(--accent);
        }

        .a-body {
            flex: 1;
            min-width: 0;
        }

        .a-desc {
            font-size: .86rem;
            font-weight: 500;
            color: var(--t1);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .a-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            flex-wrap: wrap;
        }

        .a-time {
            font-size: .74rem;
            font-weight: 500;
            color: var(--t4);
        }

        .a-user {
            font-size: .74rem;
            font-weight: 600;
            color: var(--t3);
        }

        .a-ip {
            font-size: .7rem;
            font-weight: 500;
            color: var(--t4);
        }

        .a-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .b-ev {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 7px;
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .b-ev i {
            font-size: .72rem;
        }

        .b-ev--green {
            background: rgba(34, 197, 94, .12);
            color: var(--success);
        }

        .b-ev--blue {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        .b-ev--red {
            background: rgba(248, 113, 113, .12);
            color: var(--danger);
        }

        .b-ev--amber {
            background: rgba(251, 191, 36, .12);
            color: var(--warning);
        }

        [data-theme="light"] .b-ev--green {
            background: rgba(22, 163, 74, .10);
            color: #15803d;
        }

        [data-theme="light"] .b-ev--amber {
            background: rgba(245, 158, 11, .10);
            color: #92400e;
        }

        [data-theme="light"] .b-ev--red {
            background: rgba(239, 68, 68, .10);
            color: #b91c1c;
        }

        [data-theme="light"] .b-ev--blue {
            background: rgba(59, 130, 246, .10);
            color: #1d4ed8;
        }

        [data-theme="dark"] .b-ev--amber {
            background: rgba(251, 191, 36, .14);
            color: #fde68a;
        }

        .b-mod {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 7px;
            font-size: .7rem;
            font-weight: 600;
            background: var(--input-bg);
            color: var(--t3);
            white-space: nowrap;
        }

        .b-mod i {
            font-size: .72rem;
        }

        .a-chev {
            color: var(--t4);
            font-size: .78rem;
            transition: transform .2s ease;
            flex-shrink: 0;
        }

        .a-card.open .a-chev {
            transform: rotate(180deg);
        }

        /* ── Details panel (animated) ── */

        .a-details {
            border-top: 1px solid var(--card-border);
            padding: 0 18px;
            background: var(--input-bg);
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height .3s ease, opacity .25s ease, padding .3s ease;
        }

        .a-card.open .a-details {
            max-height: 600px;
            opacity: 1;
            padding: 14px 18px;
        }

        .ch-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .ch-item {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 10px;
            align-items: start;
            font-size: .8rem;
        }

        .ch-field {
            font-weight: 600;
            color: var(--t2);
            padding-top: 2px;
        }

        .ch-vals {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ch-v {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 500;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ch-v--old {
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
            text-decoration: line-through;
        }

        .ch-v--new {
            background: rgba(34, 197, 94, .1);
            color: var(--success);
        }

        .ch-v--add {
            background: rgba(34, 197, 94, .1);
            color: var(--success);
        }

        .ch-v--rem {
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
        }

        [data-theme="light"] .ch-v--old,
        [data-theme="light"] .ch-v--rem {
            background: rgba(239, 68, 68, .08);
            color: #b91c1c;
        }

        [data-theme="light"] .ch-v--new,
        [data-theme="light"] .ch-v--add {
            background: rgba(22, 163, 74, .08);
            color: #15803d;
        }

        .ch-arrow {
            color: var(--t4);
            font-size: .72rem;
        }

        .no-ch {
            font-size: .8rem;
            color: var(--t4);
            font-style: italic;
        }

        /* ── Empty state ── */

        .h-empty {
            text-align: center;
            padding: 64px 20px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
        }

        .h-empty i {
            font-size: 2.8rem;
            color: var(--t4);
            margin-bottom: 14px;
            display: block;
        }

        .h-empty__t {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t2);
            margin-bottom: 4px;
        }

        .h-empty__p {
            font-size: .86rem;
            color: var(--t3);
        }

        /* ── Pagination ── */

        .h-pag {
            display: flex;
            justify-content: center;
            padding: 20px 0 8px;
        }

        .h-pag nav {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .h-pag nav>div:first-child {
            display: none;
        }

        .h-pag nav>div:last-child {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .h-pag nav>div:last-child>div:first-child {
            display: none;
        }

        .h-pag span[aria-current="page"]>span,
        .h-pag a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 9px;
            font-size: .82rem;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            transition: all .2s;
        }

        .h-pag span[aria-current="page"]>span {
            background: var(--accent);
            color: #fff;
        }

        .h-pag a {
            color: var(--t3);
            border: 1px solid var(--card-border);
        }

        .h-pag a:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
            background: var(--hover);
        }

        .h-pag span.disabled>span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 9px;
            font-size: .82rem;
            font-weight: 600;
            color: var(--t4);
            border: 1px solid var(--card-border);
            opacity: .5;
        }

        .a-inline-badges {
            display: none;
        }

        /* ── Responsive ── */

        @media (max-width: 1023px) {
            .h-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .h-grid {
                grid-template-columns: 1fr 1fr;
            }

            .h-acts {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .h-stat {
                padding: 14px 16px;
                gap: 12px;
            }

            .h-stat__ic {
                width: 38px;
                height: 38px;
                font-size: .92rem;
            }

            .h-stat__val {
                font-size: 1.3rem;
            }

            .a-main {
                padding: 12px 14px;
                gap: 10px;
            }

            .a-avatar {
                width: 34px;
                height: 34px;
                font-size: .64rem;
            }

            .a-desc {
                font-size: .82rem;
            }

            .a-badges {
                flex-direction: column;
                align-items: flex-end;
                gap: 4px;
            }

            .ch-item {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .a-details,
            .a-card.open .a-details {
                padding-left: 14px;
                padding-right: 14px;
            }

            .h-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .h-filters {
                padding: 12px;
            }

            .h-chip {
                height: 32px;
                padding: 0 12px;
                font-size: .76rem;
            }

            .h-pag span[aria-current="page"]>span,
            .h-pag a,
            .h-pag span.disabled>span {
                min-width: 32px;
                height: 32px;
                font-size: .78rem;
                padding: 0 8px;
            }
        }

        @media (max-width: 425px) {
            .h-stats {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .h-stat {
                padding: 12px 14px;
                gap: 10px;
                border-radius: 12px;
            }

            .h-stat__ic {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                font-size: .88rem;
            }

            .h-stat__val {
                font-size: 1.2rem;
            }

            .h-stat__lbl {
                font-size: .72rem;
            }

            .h-filters {
                padding: 10px;
                border-radius: 12px;
            }

            .h-chips {
                gap: 6px;
                margin-bottom: 10px;
            }

            .h-chip {
                height: 30px;
                padding: 0 10px;
                font-size: .74rem;
                border-radius: 7px;
            }

            .h-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .a-card {
                border-radius: 12px;
            }

            .a-main {
                padding: 10px 12px;
                gap: 10px;
            }

            .a-avatar {
                width: 32px;
                height: 32px;
                font-size: .62rem;
                border-radius: 8px;
            }

            .a-desc {
                font-size: .8rem;
            }

            .a-meta {
                gap: 6px;
                margin-top: 3px;
            }

            .a-time,
            .a-user {
                font-size: .7rem;
            }

            .a-ip {
                font-size: .66rem;
            }

            .b-ev,
            .b-mod {
                padding: 3px 7px;
                font-size: .66rem;
                border-radius: 6px;
            }

            .a-details,
            .a-card.open .a-details {
                padding-left: 12px;
                padding-right: 12px;
            }

            .ch-v {
                font-size: .74rem;
                max-width: 180px;
                padding: 2px 8px;
            }

            .ch-field {
                font-size: .74rem;
            }

            .ch-item {
                font-size: .74rem;
            }

            .h-empty {
                padding: 40px 16px;
                border-radius: 12px;
            }

            .h-empty i {
                font-size: 2.2rem;
                margin-bottom: 10px;
            }

            .h-empty__t {
                font-size: .9rem;
            }

            .h-empty__p {
                font-size: .8rem;
            }
        }

        @media (max-width: 375px) {
            .h-stat__val {
                font-size: 1.1rem;
            }

            .a-badges {
                display: none;
            }

            .a-inline-badges {
                display: flex !important;
                gap: 4px;
                margin-top: 4px;
            }
        }

        /* ── Print ── */

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
            .h-filters,
            .h-stats,
            .h-pag,
            .act-btn-label,
            .page-header__sub,
            .a-chev,
            .cell-add {
                display: none !important;
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

            .a-card {
                border: 1px solid #ccc !important;
                border-radius: 0 !important;
                page-break-inside: avoid;
                background: #fff !important;
            }

            .a-details {
                max-height: none !important;
                opacity: 1 !important;
                padding: 8px 12px !important;
                display: block !important;
                background: #f9fafb !important;
            }

            .a-main {
                padding: 8px 12px !important;
            }

            .a-avatar {
                width: 24px !important;
                height: 24px !important;
                font-size: .5rem !important;
            }

            .a-desc {
                font-size: 8px !important;
                -webkit-line-clamp: unset !important;
                color: #000 !important;
            }

            .b-ev,
            .b-mod {
                font-size: 6px !important;
                padding: 2px 5px !important;
            }

            .tl-day__hdr {
                padding: 6px 0 4px !important;
            }

            .tl-day__lbl {
                font-size: 8px !important;
                color: #000 !important;
            }

            .ch-v {
                font-size: 7px !important;
                max-width: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="h-stats">
        <div class="h-stat">
            <div class="h-stat__ic h-stat__ic--blue"><i class="fi fi-rr-time-past"></i></div>
            <div>
                <div class="h-stat__val">{{ number_format($stats['total']) }}</div>
                <div class="h-stat__lbl">Total de ações</div>
            </div>
        </div>
        <div class="h-stat">
            <div class="h-stat__ic h-stat__ic--green"><i class="fi fi-rr-calendar-day"></i></div>
            <div>
                <div class="h-stat__val">{{ $stats['hoje'] }}</div>
                <div class="h-stat__lbl">Hoje</div>
            </div>
        </div>
        <div class="h-stat">
            <div class="h-stat__ic h-stat__ic--amber"><i class="fi fi-rr-flame"></i></div>
            <div>
                <div class="h-stat__val">{{ $stats['semana'] }}</div>
                <div class="h-stat__lbl">Esta semana</div>
            </div>
        </div>
        <div class="h-stat">
            <div class="h-stat__ic h-stat__ic--purple"><i class="fi fi-rr-edit"></i></div>
            <div>
                <div class="h-stat__val">
                    {{ ($stats['por_evento']['created'] ?? 0) + ($stats['por_evento']['deleted'] ?? 0) }}</div>
                <div class="h-stat__lbl">Criações / Exclusões</div>
            </div>
        </div>
    </div>

    <div class="h-filters">
        <div class="h-chips">
            <a href="{{ route('historico.index', request()->except('modelo')) }}"
                class="h-chip {{ !request('modelo') ? 'active' : '' }}">
                <i class="fi fi-rr-apps"></i> Tudo
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'reserva'])) }}"
                class="h-chip {{ request('modelo') === 'reserva' ? 'active' : '' }}">
                <i class="fi fi-rr-calendar"></i> Reservas
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'usuario'])) }}"
                class="h-chip {{ request('modelo') === 'usuario' ? 'active' : '' }}">
                <i class="fi fi-rr-user"></i> Usuários
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'area'])) }}"
                class="h-chip {{ request('modelo') === 'area' ? 'active' : '' }}">
                <i class="fi fi-rr-marker"></i> Áreas
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'cliente'])) }}"
                class="h-chip {{ request('modelo') === 'cliente' ? 'active' : '' }}">
                <i class="fi fi-rr-user"></i> Clientes
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'pagamento'])) }}"
                class="h-chip {{ request('modelo') === 'pagamento' ? 'active' : '' }}">
                <i class="fi fi-rr-receipt"></i> Pagamentos
            </a>
            <a href="{{ route('historico.index', array_merge(request()->except('modelo'), ['modelo' => 'taxa'])) }}"
                class="h-chip {{ request('modelo') === 'taxa' ? 'active' : '' }}">
                <i class="fi fi-rr-dollar"></i> Taxas
            </a>
        </div>

        <form method="GET" action="{{ route('historico.index') }}">
            @if (request('modelo'))
                <input type="hidden" name="modelo" value="{{ request('modelo') }}">
            @endif
            <div class="h-grid">
                <div>
                    <label class="h-lbl">Evento</label>
                    <select name="evento" class="h-sel">
                        <option value="">Todos</option>
                        <option value="created" {{ request('evento') === 'created' ? 'selected' : '' }}>Criação</option>
                        <option value="updated" {{ request('evento') === 'updated' ? 'selected' : '' }}>Atualização
                        </option>
                        <option value="deleted" {{ request('evento') === 'deleted' ? 'selected' : '' }}>Exclusão</option>
                    </select>
                </div>
                <div>
                    <label class="h-lbl">Usuário</label>
                    <select name="usuario_id" class="h-sel">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="h-lbl">Busca</label>
                    <input type="text" name="busca" class="h-input" value="{{ request('busca') }}"
                        placeholder="Buscar nos dados...">
                </div>
                <div>
                    <label class="h-lbl">De</label>
                    <input type="date" name="data_inicio" class="h-input" value="{{ request('data_inicio') }}">
                </div>
                <div>
                    <label class="h-lbl">Até</label>
                    <input type="date" name="data_fim" class="h-input" value="{{ request('data_fim') }}">
                </div>
                <div class="h-acts">
                    <button type="submit" class="h-btn-go" title="Filtrar"><i class="fi fi-rr-search"></i></button>
                    <a href="{{ route('historico.index') }}" class="h-btn-clr" title="Limpar"><i
                            class="fi fi-rr-cross-small"></i></a>
                </div>
            </div>
        </form>
    </div>

    @if ($audits->isEmpty())
        <div class="h-empty">
            <i class="fi fi-rr-time-past"></i>
            <div class="h-empty__t">Nenhuma atividade encontrada</div>
            <p class="h-empty__p">Ajuste os filtros ou aguarde novas ações no sistema</p>
        </div>
    @else
        <div class="timeline">
            @foreach ($grouped as $date => $group)
                <div class="tl-day">
                    <div class="tl-day__hdr">
                        <div class="tl-day__dot"></div>
                        <span class="tl-day__lbl">{{ $group->label }}</span>
                        <div class="tl-day__line"></div>
                        <span class="tl-day__cnt">{{ $group->items->count() }}
                            {{ $group->items->count() === 1 ? 'ação' : 'ações' }}</span>
                    </div>

                    @foreach ($group->items as $audit)
                        <div class="a-card">
                            <div class="a-main" onclick="toggleCard(this.parentElement)">
                                <div class="a-avatar">{{ $audit->user_initials }}</div>
                                <div class="a-body">
                                    <div class="a-desc">{{ $audit->description }}</div>
                                    <div class="a-meta">
                                        <span class="a-time">{{ $audit->created_at->format('H:i') }}</span>
                                        <span class="a-user"><i class="fi fi-rr-user"
                                                style="font-size:.66rem;margin-right:2px"></i>{{ $audit->user_name }}</span>
                                        @if ($audit->ip)
                                            <span class="a-ip">{{ $audit->ip }}</span>
                                        @endif
                                    </div>
                                    <div class="a-inline-badges">
                                        <span
                                            class="b-ev b-ev--{{ $eventConfig[$audit->event]['color'] ?? 'blue' }}">{{ $audit->event_label }}</span>
                                        <span class="b-mod"><i
                                                class="fi {{ $modelConfig[$audit->model_type]['icon'] ?? 'fi-rr-document' }}"></i>
                                            {{ $audit->model_label }}</span>
                                    </div>
                                </div>
                                <div class="a-badges">
                                    <span class="b-ev b-ev--{{ $eventConfig[$audit->event]['color'] ?? 'blue' }}"><i
                                            class="fi {{ $eventConfig[$audit->event]['icon'] ?? 'fi-rr-info' }}"></i>
                                        {{ $audit->event_label }}</span>
                                    <span class="b-mod"><i
                                            class="fi {{ $modelConfig[$audit->model_type]['icon'] ?? 'fi-rr-document' }}"></i>
                                        {{ $audit->model_label }}</span>
                                </div>
                                <i class="fi fi-rr-angle-small-down a-chev"></i>
                            </div>
                            <div class="a-details">
                                @if (empty($audit->changes))
                                    <p class="no-ch">Sem detalhes de alteração disponíveis</p>
                                @else
                                    <div class="ch-list">
                                        @foreach ($audit->changes as $change)
                                            <div class="ch-item">
                                                <div class="ch-field">{{ $change['field'] }}</div>
                                                <div class="ch-vals">
                                                    @if ($change['type'] === 'changed')
                                                        <span class="ch-v ch-v--old">{{ $change['old'] }}</span>
                                                        <i class="fi fi-rr-arrow-right ch-arrow"></i>
                                                        <span class="ch-v ch-v--new">{{ $change['new'] }}</span>
                                                    @elseif ($change['type'] === 'added')
                                                        <span class="ch-v ch-v--add">{{ $change['new'] }}</span>
                                                    @elseif ($change['type'] === 'removed')
                                                        <span class="ch-v ch-v--rem">{{ $change['old'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="h-pag">
            {{ $audits->withQueryString()->links() }}
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        function toggleCard(el) {
            el.classList.toggle('open');
        }
    </script>
@endsection
