@extends('layouts.app')

@section('title', 'Usuários')

@section('page-header')
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-header__title">Usuários</h1>
            <p class="page-header__sub">{{ $usuarios->count() }}
                {{ $usuarios->count() === 1 ? 'usuário cadastrado' : 'usuários cadastrados' }}</p>
        </div>
        @can('usuarios.criar')
            <button class="btn-primary" onclick="abrirModalUsuario()">
                <i class="fi fi-rr-plus-small"></i> Novo Usuário
            </button>
        @endcan
    </div>
@endsection

@section('styles')
    <style>
        /* ── Stats ── */

        .u-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .u-stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: background .3s, border-color .3s;
        }

        .u-stat__ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .u-stat__ic--blue {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .u-stat__ic--purple {
            background: rgba(167, 139, 250, .15);
            color: var(--purple);
        }

        .u-stat__ic--green {
            background: rgba(52, 211, 153, .15);
            color: var(--success);
        }

        [data-theme="light"] .u-stat__ic--blue {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .u-stat__ic--purple {
            background: rgba(139, 92, 246, .10);
        }

        [data-theme="light"] .u-stat__ic--green {
            background: rgba(16, 185, 129, .10);
        }

        .u-stat__val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
        }

        .u-stat__lbl {
            font-size: .78rem;
            font-weight: 500;
            color: var(--t3);
            margin-top: 2px;
        }

        /* ── User list ── */

        .u-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .u-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: background .3s, border-color .3s;
        }

        .u-card:hover {
            border-color: var(--input-border-h);
        }

        .u-avatar {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .78rem;
            color: #fff;
            flex-shrink: 0;
        }

        .u-avatar--admin {
            background: var(--purple);
        }

        .u-avatar--usuario {
            background: var(--accent);
        }

        .u-info {
            flex: 1;
            min-width: 0;
        }

        .u-info__top {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .u-name {
            font-size: .92rem;
            font-weight: 700;
            color: var(--t1);
        }

        .u-role {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 6px;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .u-role--admin {
            background: rgba(167, 139, 250, .15);
            color: var(--purple);
        }

        .u-role--usuario {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        [data-theme="light"] .u-role--admin {
            background: rgba(139, 92, 246, .10);
            color: #6d28d9;
        }

        [data-theme="light"] .u-role--usuario {
            background: rgba(59, 130, 246, .08);
            color: #1d4ed8;
        }

        .u-self {
            font-size: .64rem;
            font-weight: 700;
            color: var(--success);
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(52, 211, 153, .12);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        [data-theme="light"] .u-self {
            background: rgba(16, 185, 129, .08);
            color: #047857;
        }

        .u-email {
            font-size: .82rem;
            color: var(--t3);
            margin-top: 2px;
        }

        .u-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .u-meta__item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: .72rem;
            font-weight: 500;
            color: var(--t4);
        }

        .u-meta__item i {
            font-size: .7rem;
        }

        /* ── Permission tags ── */

        .u-perms {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .u-perm-tag {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: .66rem;
            font-weight: 600;
        }

        .u-perm-tag--ver {
            background: rgba(91, 156, 246, .08);
            color: var(--accent);
        }

        .u-perm-tag--criar {
            background: rgba(52, 211, 153, .08);
            color: var(--success);
        }

        .u-perm-tag--editar {
            background: rgba(251, 191, 36, .08);
            color: var(--warning);
        }

        .u-perm-tag--excluir {
            background: rgba(248, 113, 113, .08);
            color: var(--danger);
        }

        .u-perm-tag--more {
            background: var(--input-bg);
            color: var(--t3);
            border: 1px solid var(--card-border);
        }

        [data-theme="light"] .u-perm-tag--ver {
            background: rgba(59, 130, 246, .06);
            color: #1d4ed8;
        }

        [data-theme="light"] .u-perm-tag--criar {
            background: rgba(16, 185, 129, .06);
            color: #047857;
        }

        [data-theme="light"] .u-perm-tag--editar {
            background: rgba(245, 158, 11, .06);
            color: #92400e;
        }

        [data-theme="light"] .u-perm-tag--excluir {
            background: rgba(239, 68, 68, .06);
            color: #b91c1c;
        }

        /* ── Action buttons ── */

        .u-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            padding: 0 20px;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s ease;
        }

        .btn-primary:hover {
            background: var(--accent-h);
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-primary i {
            font-size: .9rem;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            padding: 0 16px;
            background: transparent;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            color: var(--t3);
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
        }

        .btn-ghost:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
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
            font-size: .85rem;
            transition: all .2s ease;
        }

        .btn-ic:hover {
            color: var(--t1);
            border-color: var(--input-border-h);
            background: var(--hover);
        }

        .btn-ic.danger:hover {
            color: var(--danger);
            border-color: rgba(248, 113, 113, .3);
            background: rgba(248, 113, 113, .06);
        }

        /* ── Form inputs ── */

        .sdb-input,
        .sdb-select {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            color: var(--t1);
            font-family: inherit;
            font-size: .86rem;
            font-weight: 500;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .sdb-input::placeholder {
            color: var(--t4);
            font-weight: 400;
        }

        .sdb-input:hover,
        .sdb-select:hover {
            border-color: var(--input-border-h);
        }

        .sdb-input:focus,
        .sdb-select:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--input-glow);
        }

        /* ── Dark/Light mode: inputs ── */

        .sdb-select {
            color-scheme: dark;
        }

        [data-theme="light"] .sdb-select {
            color-scheme: light;
        }

        [data-theme="dark"] .sdb-input,
        [data-theme="dark"] .sdb-select {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .10);
            color: #eef2f7;
        }

        [data-theme="dark"] .sdb-input:focus,
        [data-theme="dark"] .sdb-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91, 156, 246, .15);
        }

        [data-theme="dark"] .sdb-input::placeholder {
            color: #4a5a74;
        }

        [data-theme="light"] .sdb-input::placeholder {
            color: #94a3b8;
        }

        [data-theme="dark"] .sdb-select option {
            background: #1a2236;
            color: #eef2f7;
            padding: 8px 12px;
        }

        [data-theme="dark"] .sdb-select option:checked {
            background: #2a3a56;
            color: #fff;
        }

        [data-theme="light"] .sdb-select option {
            background: #ffffff;
            color: #0f172a;
            padding: 8px 12px;
        }

        [data-theme="light"] .sdb-select option:checked {
            background: #e0edff;
            color: #1d4ed8;
        }

        .sdb-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--t2);
            margin-bottom: 6px;
        }

        .sdb-label .req {
            color: var(--danger);
        }

        .sdb-label .hint {
            color: var(--t4);
            font-weight: 500;
        }

        .field {
            margin-bottom: 16px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        /* ── Modal ── */

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
            transition: opacity .25s ease, visibility .25s ease;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            width: 100%;
            max-width: 560px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .3);
            transform: translateY(12px) scale(.98);
            opacity: 0;
            transition: transform .25s ease, opacity .2s ease;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.is-open .modal-box {
            transform: translateY(0) scale(1);
            opacity: 1;
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

        /* ── Role cards ── */

        .role-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 6px;
        }

        .role-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 16px 12px;
            border: 2px solid var(--card-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all .2s ease;
            text-align: center;
            user-select: none;
        }

        .role-card:hover {
            border-color: var(--input-border-h);
            background: var(--hover);
        }

        .role-card.active--admin {
            border-color: var(--purple);
            background: rgba(167, 139, 250, .06);
        }

        .role-card.active--usuario {
            border-color: var(--accent);
            background: rgba(91, 156, 246, .06);
        }

        [data-theme="light"] .role-card.active--admin {
            background: rgba(139, 92, 246, .04);
        }

        [data-theme="light"] .role-card.active--usuario {
            background: rgba(59, 130, 246, .04);
        }

        .role-card__ic {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all .2s ease;
        }

        .role-card__ic--admin {
            background: rgba(167, 139, 250, .15);
            color: var(--purple);
        }

        .role-card__ic--usuario {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        [data-theme="light"] .role-card__ic--admin {
            background: rgba(139, 92, 246, .10);
        }

        [data-theme="light"] .role-card__ic--usuario {
            background: rgba(59, 130, 246, .10);
        }

        .role-card__name {
            font-size: .82rem;
            font-weight: 700;
            color: var(--t1);
        }

        .role-card__desc {
            font-size: .7rem;
            color: var(--t3);
            line-height: 1.3;
        }

        /* ── Permissions panel ── */

        .perms-wrap {
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 14px;
            transition: background .3s, border-color .3s;
        }

        .perms-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .perms-header__title {
            font-size: .78rem;
            font-weight: 700;
            color: var(--t2);
        }

        .perms-header__hint {
            font-size: .7rem;
            color: var(--t4);
            font-weight: 500;
            font-style: italic;
        }

        .perm-group {
            margin-bottom: 12px;
        }

        .perm-group:last-child {
            margin-bottom: 0;
        }

        .perm-group__head {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid var(--card-border);
        }

        .perm-group__ic {
            width: 22px;
            height: 22px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
        }

        .perm-group__ic--areas {
            background: rgba(52, 211, 153, .12);
            color: var(--success);
        }

        .perm-group__ic--reservas {
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        .perm-group__ic--usuarios {
            background: rgba(167, 139, 250, .12);
            color: var(--purple);
        }

        [data-theme="light"] .perm-group__ic--areas {
            background: rgba(16, 185, 129, .08);
        }

        [data-theme="light"] .perm-group__ic--reservas {
            background: rgba(59, 130, 246, .08);
        }

        [data-theme="light"] .perm-group__ic--usuarios {
            background: rgba(139, 92, 246, .08);
        }

        .perm-group__label {
            font-size: .74rem;
            font-weight: 700;
            color: var(--t2);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .perm-group__toggle {
            margin-left: auto;
            font-size: .66rem;
            font-weight: 600;
            color: var(--accent);
            cursor: pointer;
            border: none;
            background: none;
            font-family: inherit;
            padding: 2px 6px;
            border-radius: 4px;
            transition: background .15s ease;
        }

        .perm-group__toggle:hover {
            background: rgba(91, 156, 246, .08);
        }

        .perm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            padding: 6px 8px;
            border-radius: 8px;
            transition: background .15s ease;
        }

        .perm-item:hover {
            background: var(--hover);
        }

        .perm-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid var(--input-border);
            background: var(--card);
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: all .2s ease;
        }

        .perm-item input[type="checkbox"]:hover {
            border-color: var(--accent);
        }

        .perm-item input[type="checkbox"]:checked {
            background: var(--accent);
            border-color: var(--accent);
        }

        .perm-item input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 1px;
            width: 5px;
            height: 9px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        [data-theme="dark"] .perm-item input[type="checkbox"] {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .15);
        }

        .perm-item__label {
            font-size: .78rem;
            font-weight: 500;
            color: var(--t2);
        }

        .perm-item__icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .6rem;
            flex-shrink: 0;
        }

        .perm-item__icon--ver {
            background: rgba(91, 156, 246, .1);
            color: var(--accent);
        }

        .perm-item__icon--criar {
            background: rgba(52, 211, 153, .1);
            color: var(--success);
        }

        .perm-item__icon--editar {
            background: rgba(251, 191, 36, .1);
            color: var(--warning);
        }

        .perm-item__icon--excluir {
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
        }

        [data-theme="light"] .perm-item__icon--editar {
            color: #92400e;
        }

        [data-theme="light"] .perm-item__icon--excluir {
            color: #b91c1c;
        }

        [data-theme="light"] .perm-item__icon--criar {
            color: #047857;
        }

        /* ── Confirm dialog ── */

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
            transition: opacity .2s ease, visibility .2s ease;
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
            border-radius: 18px;
            padding: 28px 24px 22px;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .3);
            transform: scale(.95);
            opacity: 0;
            transition: transform .2s ease, opacity .2s ease;
        }

        .confirm-overlay.is-open .confirm-box {
            transform: scale(1);
            opacity: 1;
        }

        .confirm-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            background: rgba(248, 113, 113, .1);
            color: var(--danger);
        }

        [data-theme="light"] .confirm-icon {
            background: rgba(239, 68, 68, .08);
            color: #b91c1c;
        }

        .confirm-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 6px;
        }

        .confirm-msg {
            font-size: .86rem;
            color: var(--t3);
            margin-bottom: 22px;
            line-height: 1.5;
        }

        .confirm-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-danger-solid {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            padding: 0 20px;
            background: var(--danger);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s ease;
        }

        .btn-danger-solid:hover {
            background: #dc2626;
        }

        /* ── Empty state ── */

        .u-empty {
            text-align: center;
            padding: 64px 20px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
        }

        .u-empty i {
            font-size: 2.8rem;
            color: var(--t4);
            margin-bottom: 14px;
            display: block;
        }

        .u-empty__t {
            font-size: 1rem;
            font-weight: 700;
            color: var(--t2);
            margin-bottom: 4px;
        }

        .u-empty__p {
            font-size: .86rem;
            color: var(--t3);
        }

        /* ── Responsive ── */

        @media (max-width: 1023px) {
            .u-perms {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .u-stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .u-stat {
                padding: 14px 16px;
                gap: 12px;
            }

            .u-stat__ic {
                width: 38px;
                height: 38px;
                font-size: .92rem;
            }

            .u-stat__val {
                font-size: 1.3rem;
            }

            .u-card {
                padding: 14px 16px;
                gap: 12px;
            }

            .u-avatar {
                width: 40px;
                height: 40px;
                font-size: .72rem;
                border-radius: 10px;
            }

            .u-name {
                font-size: .86rem;
            }

            .u-email {
                font-size: .78rem;
            }

            .u-actions {
                margin-left: auto;
            }

            .modal-overlay {
                padding: 12px;
            }

            .modal-box {
                max-height: 88vh;
                border-radius: 16px;
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
        }

        @media (max-width: 425px) {
            .u-stats {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 8px;
            }

            .u-stat {
                padding: 10px;
                gap: 8px;
                border-radius: 12px;
                flex-direction: column;
                align-items: flex-start;
            }

            .u-stat__ic {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                font-size: .82rem;
            }

            .u-stat__val {
                font-size: 1.15rem;
            }

            .u-stat__lbl {
                font-size: .68rem;
            }

            .u-card {
                padding: 12px 14px;
                gap: 10px;
                border-radius: 12px;
            }

            .u-avatar {
                width: 36px;
                height: 36px;
                font-size: .66rem;
                border-radius: 9px;
            }

            .u-name {
                font-size: .84rem;
            }

            .u-role {
                font-size: .64rem;
                padding: 2px 7px;
            }

            .u-email {
                font-size: .76rem;
            }

            .u-meta {
                gap: 8px;
            }

            .u-meta__item {
                font-size: .68rem;
            }

            .btn-ic {
                width: 32px;
                height: 32px;
                font-size: .8rem;
                border-radius: 7px;
            }

            .modal-overlay {
                padding: 8px;
            }

            .modal-box {
                max-height: 90vh;
                border-radius: 14px;
            }

            .modal-head {
                padding: 12px 14px;
            }

            .modal-head__title {
                font-size: .9rem;
            }

            .modal-body {
                padding: 14px;
            }

            .modal-foot {
                padding: 10px 14px;
            }

            .sdb-input,
            .sdb-select {
                height: 40px;
                font-size: .82rem;
            }

            .sdb-label {
                font-size: .74rem;
                margin-bottom: 4px;
            }

            .field {
                margin-bottom: 12px;
            }

            .role-cards {
                gap: 8px;
            }

            .role-card {
                padding: 12px 10px;
                border-radius: 10px;
            }

            .role-card__ic {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                font-size: .9rem;
            }

            .role-card__name {
                font-size: .78rem;
            }

            .role-card__desc {
                font-size: .66rem;
            }

            .perm-grid {
                grid-template-columns: 1fr;
            }

            .perms-wrap {
                padding: 12px;
                border-radius: 10px;
            }

            .btn-primary,
            .btn-ghost,
            .btn-danger-solid {
                height: 38px;
                font-size: .8rem;
                padding: 0 14px;
            }

            .confirm-box {
                border-radius: 14px;
                padding: 22px 18px 18px;
            }

            .confirm-icon {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }

            .confirm-title {
                font-size: .92rem;
            }

            .confirm-msg {
                font-size: .8rem;
                margin-bottom: 18px;
            }

            .u-empty {
                padding: 40px 16px;
                border-radius: 12px;
            }

            .u-empty i {
                font-size: 2.2rem;
                margin-bottom: 10px;
            }

            .u-empty__t {
                font-size: .9rem;
            }

            .u-empty__p {
                font-size: .8rem;
            }
        }

        @media (max-width: 375px) {
            .u-stat {
                padding: 8px;
            }

            .u-stat__val {
                font-size: 1.05rem;
            }

            .u-stat__lbl {
                font-size: .64rem;
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
            .u-stats,
            .u-actions,
            .btn-primary,
            .page-header__sub,
            .modal-overlay,
            .confirm-overlay {
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

            .u-card {
                border: 1px solid #ccc !important;
                border-radius: 0 !important;
                padding: 8px 12px !important;
                background: #fff !important;
                page-break-inside: avoid;
            }

            .u-avatar {
                width: 24px !important;
                height: 24px !important;
                font-size: .5rem !important;
                border-radius: 4px !important;
            }

            .u-name {
                font-size: 9px !important;
                color: #000 !important;
            }

            .u-email {
                font-size: 8px !important;
                color: #333 !important;
            }

            .u-role {
                font-size: 6px !important;
                padding: 1px 4px !important;
            }

            .u-meta__item {
                font-size: 7px !important;
                color: #555 !important;
            }

            .u-perms {
                display: flex !important;
            }

            .u-perm-tag {
                font-size: 6px !important;
                padding: 1px 4px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="u-stats">
        <div class="u-stat">
            <div class="u-stat__ic u-stat__ic--blue"><i class="fi fi-rr-users"></i></div>
            <div>
                <div class="u-stat__val">{{ $totalUsers }}</div>
                <div class="u-stat__lbl">Total</div>
            </div>
        </div>
        <div class="u-stat">
            <div class="u-stat__ic u-stat__ic--purple"><i class="fi fi-rr-shield-check"></i></div>
            <div>
                <div class="u-stat__val">{{ $admins }}</div>
                <div class="u-stat__lbl">Administradores</div>
            </div>
        </div>
        <div class="u-stat">
            <div class="u-stat__ic u-stat__ic--green"><i class="fi fi-rr-user"></i></div>
            <div>
                <div class="u-stat__val">{{ $regulars }}</div>
                <div class="u-stat__lbl">Usuários</div>
            </div>
        </div>
    </div>

    @if ($usuarios->isEmpty())
        <div class="u-empty">
            <i class="fi fi-rr-users"></i>
            <div class="u-empty__t">Nenhum usuário cadastrado</div>
            <p class="u-empty__p">Crie o primeiro usuário para começar</p>
        </div>
    @else
        <div class="u-list">
            @foreach ($usuarios as $usuario)
                <div class="u-card">
                    <div class="u-avatar u-avatar--{{ $usuario->role_name }}">{{ $usuario->initials }}
                    </div>
                    <div class="u-info">
                        <div class="u-info__top">
                            <span class="u-name">{{ $usuario->name }}</span>
                            <span class="u-role u-role--{{ $usuario->role_name }}">
                                <i class="fi {{ $usuario->role_name === 'admin' ? 'fi-rr-shield-check' : 'fi-rr-user' }}"
                                    style="font-size:.6rem"></i>
                                {{ $usuario->role_name }}
                            </span>
                            @if ($usuario->is_self)
                                <span class="u-self">você</span>
                            @endif
                        </div>
                        <div class="u-email">{{ $usuario->email }}</div>
                        <div class="u-meta">
                            <span class="u-meta__item"><i class="fi fi-rr-calendar"></i>
                                {{ $usuario->created_at->format('d/m/Y') }}</span>
                            <span class="u-meta__item"><i class="fi fi-rr-lock"></i> {{ $usuario->user_perms->count() }}
                                {{ $usuario->user_perms->count() === 1 ? 'permissão' : 'permissões' }}</span>
                        </div>
                    </div>
                    <div class="u-perms">
                        @foreach ($usuario->perm_tags->take(5) as $tag)
                            <span class="u-perm-tag u-perm-tag--{{ $tag['action'] }}">{{ $tag['action'] }}</span>
                        @endforeach
                        @if ($usuario->perm_tags->count() > 5)
                            <span class="u-perm-tag u-perm-tag--more">+{{ $usuario->perm_tags->count() - 5 }}</span>
                        @endif
                    </div>
                    <div class="u-actions">
                        @can('usuarios.editar')
                            <button class="btn-ic"
                                onclick="editarUsuario({{ Js::from($usuario->only('id', 'name', 'email')) }}, {{ Js::from($usuario->roles->pluck('name')) }}, {{ Js::from($usuario->user_perms) }})"
                                title="Editar"><i class="fi fi-rr-pencil"></i></button>
                        @endcan
                        @can('usuarios.excluir')
                            @if (!$usuario->is_self)
                                <button class="btn-ic danger" onclick="excluirUsuario({{ $usuario->id }})" title="Excluir"><i
                                        class="fi fi-rr-trash"></i></button>
                            @endif
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="modal-overlay" id="modalUsuario">
        <div class="modal-box">
            <div class="modal-head">
                <span class="modal-head__title" id="modalUsuarioTitulo">Novo Usuário</span>
                <button class="modal-head__close" onclick="fecharModal()"><i class="fi fi-rr-cross-small"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user-id">

                <div class="field">
                    <label class="sdb-label">Nome <span class="req">*</span></label>
                    <input type="text" id="user-name" class="sdb-input" placeholder="Nome completo">
                </div>

                <div class="field">
                    <label class="sdb-label">Email <span class="req">*</span></label>
                    <input type="email" id="user-email" class="sdb-input" placeholder="email@exemplo.com">
                </div>

                <div class="field">
                    <label class="sdb-label">Senha <span class="hint" id="senha-hint">(obrigatório)</span></label>
                    <input type="password" id="user-password" class="sdb-input" placeholder="Mínimo 6 caracteres">
                </div>

                <div class="field">
                    <label class="sdb-label">Perfil</label>
                    <div class="role-cards">
                        <div class="role-card" data-role="admin" onclick="selecionarRole('admin')">
                            <div class="role-card__ic role-card__ic--admin"><i class="fi fi-rr-shield-check"></i></div>
                            <div class="role-card__name">Admin</div>
                            <div class="role-card__desc">Acesso total ao sistema</div>
                        </div>
                        <div class="role-card" data-role="usuario" onclick="selecionarRole('usuario')">
                            <div class="role-card__ic role-card__ic--usuario"><i class="fi fi-rr-user"></i></div>
                            <div class="role-card__name">Usuário</div>
                            <div class="role-card__desc">Acesso a reservas e visualização</div>
                        </div>
                    </div>
                    <input type="hidden" id="user-role" value="usuario">
                </div>

                <div class="field">
                    <div class="perms-wrap">
                        <div class="perms-header">
                            <span class="perms-header__title">Permissões</span>
                            <span class="perms-header__hint" id="permsHint">Personalize após selecionar o perfil</span>
                        </div>

                        <div class="perm-group">
                            <div class="perm-group__head">
                                <div class="perm-group__ic perm-group__ic--areas"><i class="fi fi-rr-marker"></i></div>
                                <span class="perm-group__label">Áreas</span>
                                <button type="button" class="perm-group__toggle" onclick="toggleGroup('areas')">Marcar
                                    todos</button>
                            </div>
                            <div class="perm-grid">
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="areas.ver"
                                        data-group="areas">
                                    <div class="perm-item__icon perm-item__icon--ver"><i class="fi fi-rr-eye"></i></div>
                                    <span class="perm-item__label">Visualizar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="areas.criar"
                                        data-group="areas">
                                    <div class="perm-item__icon perm-item__icon--criar"><i class="fi fi-rr-plus"></i>
                                    </div><span class="perm-item__label">Criar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="areas.editar"
                                        data-group="areas">
                                    <div class="perm-item__icon perm-item__icon--editar"><i class="fi fi-rr-pencil"></i>
                                    </div><span class="perm-item__label">Editar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="areas.excluir"
                                        data-group="areas">
                                    <div class="perm-item__icon perm-item__icon--excluir"><i class="fi fi-rr-trash"></i>
                                    </div><span class="perm-item__label">Excluir</span>
                                </label>
                            </div>
                        </div>

                        <div class="perm-group">
                            <div class="perm-group__head">
                                <div class="perm-group__ic perm-group__ic--reservas"><i class="fi fi-rr-calendar"></i>
                                </div>
                                <span class="perm-group__label">Reservas</span>
                                <button type="button" class="perm-group__toggle"
                                    onclick="toggleGroup('reservas')">Marcar todos</button>
                            </div>
                            <div class="perm-grid">
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="reservas.ver"
                                        data-group="reservas">
                                    <div class="perm-item__icon perm-item__icon--ver"><i class="fi fi-rr-eye"></i></div>
                                    <span class="perm-item__label">Visualizar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="reservas.criar" data-group="reservas">
                                    <div class="perm-item__icon perm-item__icon--criar"><i class="fi fi-rr-plus"></i>
                                    </div><span class="perm-item__label">Criar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="reservas.editar" data-group="reservas">
                                    <div class="perm-item__icon perm-item__icon--editar"><i class="fi fi-rr-pencil"></i>
                                    </div><span class="perm-item__label">Editar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="reservas.excluir" data-group="reservas">
                                    <div class="perm-item__icon perm-item__icon--excluir"><i class="fi fi-rr-trash"></i>
                                    </div><span class="perm-item__label">Excluir</span>
                                </label>
                            </div>
                        </div>

                        <div class="perm-group">
                            <div class="perm-group__head">
                                <div class="perm-group__ic perm-group__ic--usuarios"><i class="fi fi-rr-users"></i></div>
                                <span class="perm-group__label">Usuários</span>
                                <button type="button" class="perm-group__toggle"
                                    onclick="toggleGroup('usuarios')">Marcar todos</button>
                            </div>
                            <div class="perm-grid">
                                <label class="perm-item"><input type="checkbox" class="perm-check" value="usuarios.ver"
                                        data-group="usuarios">
                                    <div class="perm-item__icon perm-item__icon--ver"><i class="fi fi-rr-eye"></i></div>
                                    <span class="perm-item__label">Visualizar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="usuarios.criar" data-group="usuarios">
                                    <div class="perm-item__icon perm-item__icon--criar"><i class="fi fi-rr-plus"></i>
                                    </div><span class="perm-item__label">Criar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="usuarios.editar" data-group="usuarios">
                                    <div class="perm-item__icon perm-item__icon--editar"><i class="fi fi-rr-pencil"></i>
                                    </div><span class="perm-item__label">Editar</span>
                                </label>
                                <label class="perm-item"><input type="checkbox" class="perm-check"
                                        value="usuarios.excluir" data-group="usuarios">
                                    <div class="perm-item__icon perm-item__icon--excluir"><i class="fi fi-rr-trash"></i>
                                    </div><span class="perm-item__label">Excluir</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-ghost" onclick="fecharModal()">Cancelar</button>
                <button class="btn-primary" id="btnSalvarUsuario" onclick="salvarUsuario()"><i
                        class="fi fi-rr-check"></i> Salvar</button>
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-icon"><i class="fi fi-rr-trash"></i></div>
            <div class="confirm-title">Confirmação</div>
            <div class="confirm-msg" id="confirmMsg">Deseja excluir este usuário?</div>
            <div class="confirm-actions">
                <button class="btn-ghost" onclick="fecharConfirm(false)">Cancelar</button>
                <button class="btn-danger-solid" onclick="fecharConfirm(true)"><i class="fi fi-rr-trash"></i>
                    Excluir</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var ROUTE_STORE = '{{ route('usuarios.store') }}';
        var ROUTE_UPDATE = '{{ route('usuarios.update', '__ID__') }}';
        var ROUTE_DESTROY = '{{ route('usuarios.destroy', '__ID__') }}';

        var confirmCallback = null;

        var rolePerms = @json($rolePerms);

        function selecionarRole(role) {
            document.getElementById('user-role').value = role;
            document.querySelectorAll('.role-card').forEach(function(card) {
                card.classList.remove('active--admin', 'active--usuario');
                if (card.dataset.role === role) card.classList.add('active--' + role);
            });
            var perms = rolePerms[role] || [];
            document.querySelectorAll('.perm-check').forEach(function(cb) {
                cb.checked = perms.indexOf(cb.value) !== -1;
            });
            document.getElementById('permsHint').textContent =
                role === 'admin' ? 'Todas marcadas (acesso total)' : 'Permissões padrão aplicadas';
        }

        function toggleGroup(group) {
            var checks = document.querySelectorAll('.perm-check[data-group="' + group + '"]');
            var allChecked = true;
            checks.forEach(function(cb) {
                if (!cb.checked) allChecked = false;
            });
            checks.forEach(function(cb) {
                cb.checked = !allChecked;
            });
        }

        function abrirModalUsuario() {
            document.getElementById('modalUsuarioTitulo').textContent = 'Novo Usuário';
            document.getElementById('user-id').value = '';
            document.getElementById('user-name').value = '';
            document.getElementById('user-email').value = '';
            document.getElementById('user-password').value = '';
            document.getElementById('senha-hint').textContent = '(obrigatório)';
            document.getElementById('permsHint').textContent = 'Personalize após selecionar o perfil';
            document.querySelectorAll('.perm-check').forEach(function(cb) {
                cb.checked = false;
            });
            document.querySelectorAll('.role-card').forEach(function(c) {
                c.classList.remove('active--admin', 'active--usuario');
            });
            selecionarRole('usuario');
            document.getElementById('modalUsuario').classList.add('is-open');
        }

        function editarUsuario(user, roles, permissions) {
            document.getElementById('modalUsuarioTitulo').textContent = 'Editar Usuário';
            document.getElementById('user-id').value = user.id;
            document.getElementById('user-name').value = user.name;
            document.getElementById('user-email').value = user.email;
            document.getElementById('user-password').value = '';
            document.getElementById('senha-hint').textContent = '(deixe vazio para manter)';
            document.getElementById('permsHint').textContent = 'Permissões atuais do usuário';

            var role = roles[0] || 'usuario';
            document.getElementById('user-role').value = role;
            document.querySelectorAll('.role-card').forEach(function(card) {
                card.classList.remove('active--admin', 'active--usuario');
                if (card.dataset.role === role) card.classList.add('active--' + role);
            });

            document.querySelectorAll('.perm-check').forEach(function(cb) {
                cb.checked = permissions.indexOf(cb.value) !== -1;
            });
            document.getElementById('modalUsuario').classList.add('is-open');
        }

        function fecharModal() {
            document.getElementById('modalUsuario').classList.remove('is-open');
        }

        function confirmar(texto) {
            return new Promise(function(resolve) {
                document.getElementById('confirmMsg').textContent = texto;
                document.getElementById('confirmOverlay').classList.add('is-open');
                confirmCallback = resolve;
            });
        }

        function fecharConfirm(result) {
            document.getElementById('confirmOverlay').classList.remove('is-open');
            if (confirmCallback) {
                confirmCallback(result);
                confirmCallback = null;
            }
        }

        function salvarUsuario() {
            var id = document.getElementById('user-id').value;
            var perms = [];
            document.querySelectorAll('.perm-check:checked').forEach(function(cb) {
                perms.push(cb.value);
            });

            var dados = {
                name: document.getElementById('user-name').value.trim(),
                email: document.getElementById('user-email').value.trim(),
                password: document.getElementById('user-password').value,
                role: document.getElementById('user-role').value,
                permissions: perms
            };

            if (!dados.name || !dados.email) {
                SdbToast.error('Preencha nome e email');
                return;
            }
            if (!id && !dados.password) {
                SdbToast.error('Informe a senha');
                return;
            }

            var btn = document.getElementById('btnSalvarUsuario');
            btn.disabled = true;

            fetchApi(id ? ROUTE_UPDATE.replace('__ID__', id) : ROUTE_STORE, {
                method: id ? 'PUT' : 'POST',
                body: JSON.stringify(dados)
            }).then(function() {
                fecharModal();
                SdbToast.success(id ? 'Usuário atualizado' : 'Usuário criado');
                setTimeout(function() {
                    location.reload();
                }, 800);
            }).catch(function(err) {
                if (err.status === 422) {
                    err.json().then(function(data) {
                        var msgs = Object.values(data.errors || {}).flat();
                        SdbToast.error(msgs.join(', ') || 'Dados inválidos');
                    });
                } else if (err.status === 403) {
                    SdbToast.error('Sem permissão para esta ação');
                } else {
                    SdbToast.error('Erro ao salvar usuário');
                }
            }).finally(function() {
                btn.disabled = false;
            });
        }

        function excluirUsuario(id) {
            confirmar('Deseja excluir este usuário?').then(function(ok) {
                if (!ok) return;
                fetchApi(ROUTE_DESTROY.replace('__ID__', id), {
                    method: 'DELETE'
                }).then(function() {
                    SdbToast.success('Usuário excluído');
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                }).catch(function(err) {
                    if (err.status === 403) SdbToast.error('Não é possível excluir este usuário');
                    else SdbToast.error('Erro ao excluir usuário');
                });
            });
        }

        document.getElementById('modalUsuario').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('confirmOverlay').classList.contains('is-open')) fecharConfirm(false);
                else if (document.getElementById('modalUsuario').classList.contains('is-open')) fecharModal();
            }
        });
    </script>
@endsection
