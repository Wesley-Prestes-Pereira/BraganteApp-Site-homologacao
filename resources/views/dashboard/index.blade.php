@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <h1 class="page-header__title">Dashboard</h1>
    <p class="page-header__sub">Visão geral das reservas — Complexo Bragante</p>
@endsection

@section('styles')
    <style>
        .stats,
        .resources {
            contain: layout style;
        }

        .stat,
        .res {
            contain: layout style;
        }

        .res,
        .modal-close,
        .btn-cta {
            touch-action: manipulation;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 36px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: background .3s, border-color .3s;
        }

        .stat__icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat__icon--blue {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .stat__icon--green {
            background: rgba(52, 211, 153, .15);
            color: var(--success);
        }

        .stat__icon--amber {
            background: rgba(251, 191, 36, .15);
            color: var(--warning);
        }

        .stat__icon--purple {
            background: rgba(167, 139, 250, .15);
            color: var(--purple);
        }

        [data-theme="light"] .stat__icon--blue {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .stat__icon--green {
            background: rgba(16, 185, 129, .10);
        }

        [data-theme="light"] .stat__icon--amber {
            background: rgba(245, 158, 11, .10);
        }

        [data-theme="light"] .stat__icon--purple {
            background: rgba(139, 92, 246, .10);
        }

        .stat__content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .stat__value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
            letter-spacing: -.03em;
        }

        .stat__label {
            font-size: .84rem;
            font-weight: 600;
            color: var(--t2);
            margin-top: 4px;
        }

        .sec {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .sec__dot {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .88rem;
            flex-shrink: 0;
        }

        .sec__dot--court {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .sec__dot--grill {
            background: rgba(248, 113, 113, .15);
            color: var(--danger);
        }

        [data-theme="light"] .sec__dot--court {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .sec__dot--grill {
            background: rgba(239, 68, 68, .10);
        }

        .sec__text {
            font-size: .94rem;
            font-weight: 700;
            color: var(--t1);
        }

        .sec__count {
            font-size: .76rem;
            font-weight: 700;
            color: var(--t3);
            margin-left: 2px;
        }

        .resources {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            margin-bottom: 36px;
        }

        .res {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            cursor: pointer;
            transition: border-color .18s ease, background .18s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .res:hover {
            border-color: var(--accent);
            background: var(--active);
        }

        .res__icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.08rem;
            flex-shrink: 0;
        }

        .res__icon--court {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .res__icon--grill {
            background: rgba(248, 113, 113, .15);
            color: var(--danger);
        }

        [data-theme="light"] .res__icon--court {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .res__icon--grill {
            background: rgba(239, 68, 68, .10);
        }

        .res__info {
            flex: 1;
            min-width: 0;
        }

        .res__name {
            font-size: .94rem;
            font-weight: 700;
            color: var(--t1);
            line-height: 1.3;
        }

        .res__meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .res__days {
            font-size: .8rem;
            font-weight: 500;
            color: var(--t2);
        }

        .res__tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: .72rem;
            font-weight: 700;
            background: rgba(91, 156, 246, .12);
            color: var(--accent);
        }

        [data-theme="light"] .res__tag {
            background: rgba(59, 130, 246, .10);
            color: #1d4ed8;
        }

        .res__tag i {
            font-size: .58rem;
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
            transition: opacity .22s ease, visibility .22s ease;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            width: 100%;
            max-width: 420px;
            max-height: 90vh;
            overflow-y: auto;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .28);
            transform: translateY(12px) scale(.96);
            opacity: 0;
            transition: transform .25s ease, opacity .2s ease;
        }

        .modal-overlay.is-open .modal-box {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-top {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 24px 24px 18px;
        }

        .modal-top__icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .modal-top__icon--court {
            background: rgba(91, 156, 246, .15);
            color: var(--accent);
        }

        .modal-top__icon--grill {
            background: rgba(248, 113, 113, .15);
            color: var(--danger);
        }

        [data-theme="light"] .modal-top__icon--court {
            background: rgba(59, 130, 246, .10);
        }

        [data-theme="light"] .modal-top__icon--grill {
            background: rgba(239, 68, 68, .10);
        }

        .modal-top__info {
            flex: 1;
            min-width: 0;
        }

        .modal-top__title {
            font-size: 1.14rem;
            font-weight: 700;
            color: var(--t1);
        }

        .modal-top__type {
            font-size: .8rem;
            font-weight: 600;
            color: var(--t2);
            margin-top: 2px;
        }

        .modal-close {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: none;
            color: var(--t3);
            cursor: pointer;
            font-size: .92rem;
            transition: background .12s, color .12s;
            flex-shrink: 0;
        }

        .modal-close:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .modal-details {
            padding: 0 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-row__icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .82rem;
            flex-shrink: 0;
            color: var(--t2);
        }

        [data-theme="dark"] .detail-row__icon {
            background: rgba(255, 255, 255, .06);
        }

        [data-theme="light"] .detail-row__icon {
            background: rgba(0, 0, 0, .04);
        }

        .detail-row__label {
            font-weight: 500;
            color: var(--t2);
            font-size: .8rem;
        }

        .detail-row__value {
            font-weight: 700;
            color: var(--t1);
            font-size: .92rem;
        }

        .modal-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 0 24px 20px;
        }

        .mini-stat {
            text-align: center;
            padding: 14px 10px;
            border-radius: 12px;
        }

        [data-theme="dark"] .mini-stat {
            background: rgba(255, 255, 255, .06);
        }

        [data-theme="light"] .mini-stat {
            background: rgba(0, 0, 0, .03);
        }

        .mini-stat__value {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--t1);
            line-height: 1;
            letter-spacing: -.02em;
        }

        .mini-stat__label {
            font-size: .72rem;
            font-weight: 700;
            color: var(--t2);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .mini-stat--accent .mini-stat__value {
            color: var(--accent);
        }

        .mini-stat--success .mini-stat__value {
            color: var(--success);
        }

        .mini-stat--warning .mini-stat__value {
            color: var(--warning);
        }

        [data-theme="light"] .mini-stat--success .mini-stat__value {
            color: #15803d;
        }

        [data-theme="light"] .mini-stat--warning .mini-stat__value {
            color: #b45309;
        }

        [data-theme="light"] .mini-stat--accent .mini-stat__value {
            color: #1d4ed8;
        }

        [data-theme="dark"] .mini-stat--warning .mini-stat__value {
            color: #fde68a;
        }

        .mini-stat--purple {
            background: rgba(167, 139, 250, .08);
            border-color: rgba(167, 139, 250, .15);
        }

        .mini-stat--purple .mini-stat__value {
            color: var(--purple);
        }

        .modal-cta {
            padding: 0 24px 24px;
        }

        .btn-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 46px;
            background: var(--accent);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: inherit;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-cta:hover {
            background: var(--accent-h);
        }

        .btn-cta i {
            font-size: .82rem;
        }

        @media (min-width: 1440px) {
            .stats {
                gap: 18px;
                margin-bottom: 40px;
            }

            .stat {
                padding: 24px 28px;
                gap: 18px;
            }

            .stat__icon {
                width: 52px;
                height: 52px;
                font-size: 1.16rem;
            }

            .stat__value {
                font-size: 1.9rem;
            }

            .stat__label {
                font-size: .88rem;
            }

            .resources {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 14px;
                margin-bottom: 40px;
            }

            .res {
                padding: 20px 22px;
            }

            .res__icon {
                width: 48px;
                height: 48px;
                font-size: 1.12rem;
            }

            .res__name {
                font-size: .96rem;
            }
        }

        @media (max-width: 1023px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                margin-bottom: 30px;
            }

            .stat {
                padding: 18px 20px;
                gap: 14px;
            }

            .stat__icon {
                width: 44px;
                height: 44px;
                font-size: 1.04rem;
            }

            .stat__value {
                font-size: 1.55rem;
            }

            .stat__label {
                font-size: .82rem;
            }

            .resources {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .stats {
                gap: 10px;
                margin-bottom: 28px;
            }

            .stat {
                padding: 16px 18px;
                gap: 12px;
                border-radius: 12px;
            }

            .stat__icon {
                width: 42px;
                height: 42px;
                border-radius: 10px;
                font-size: 1rem;
            }

            .stat__value {
                font-size: 1.45rem;
            }

            .stat__label {
                font-size: .8rem;
            }

            .sec__dot {
                width: 28px;
                height: 28px;
                font-size: .82rem;
            }

            .sec__text {
                font-size: .9rem;
            }

            .resources {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 10px;
                margin-bottom: 30px;
            }

            .res {
                padding: 16px 18px;
                gap: 12px;
                border-radius: 12px;
            }

            .res__icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .res__name {
                font-size: .9rem;
            }

            .res__days {
                font-size: .78rem;
            }

            .res__tag {
                font-size: .7rem;
                padding: 2px 7px;
            }

            .modal-overlay {
                padding: 16px;
            }

            .modal-box {
                border-radius: 16px;
                max-width: 100%;
            }

            .modal-top {
                padding: 20px 20px 16px;
                gap: 12px;
            }

            .modal-top__icon {
                width: 44px;
                height: 44px;
                font-size: 1.05rem;
            }

            .modal-top__title {
                font-size: 1.06rem;
            }

            .modal-top__type {
                font-size: .78rem;
            }

            .modal-close {
                width: 32px;
                height: 32px;
                font-size: .88rem;
            }

            .modal-details {
                padding: 0 20px 16px;
                gap: 10px;
            }

            .detail-row__icon {
                width: 34px;
                height: 34px;
                font-size: .8rem;
            }

            .detail-row__label {
                font-size: .78rem;
            }

            .detail-row__value {
                font-size: .88rem;
            }

            .modal-stats {
                padding: 0 20px 16px;
                gap: 8px;
            }

            .mini-stat {
                padding: 12px 8px;
                border-radius: 10px;
            }

            .mini-stat__value {
                font-size: 1.15rem;
            }

            .mini-stat__label {
                font-size: .68rem;
            }

            .modal-cta {
                padding: 0 20px 20px;
            }

            .btn-cta {
                height: 44px;
                border-radius: 10px;
                font-size: .86rem;
            }
        }

        @media (max-width: 425px) {
            .stats {
                gap: 10px;
                margin-bottom: 24px;
            }

            .stat {
                padding: 14px 16px;
                gap: 10px;
                border-radius: 12px;
            }

            .stat__icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                font-size: .94rem;
            }

            .stat__value {
                font-size: 1.3rem;
            }

            .stat__label {
                font-size: .76rem;
            }

            .sec {
                margin-bottom: 10px;
            }

            .sec__dot {
                width: 26px;
                height: 26px;
                border-radius: 7px;
                font-size: .8rem;
            }

            .sec__text {
                font-size: .86rem;
            }

            .sec__count {
                font-size: .72rem;
            }

            .resources {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-bottom: 24px;
            }

            .res {
                padding: 14px 16px;
                border-radius: 12px;
                gap: 12px;
            }

            .res__icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                font-size: .96rem;
            }

            .res__name {
                font-size: .88rem;
            }

            .res__days {
                font-size: .76rem;
            }

            .res__tag {
                font-size: .7rem;
                padding: 2px 7px;
            }

            .res__tag i {
                font-size: .56rem;
            }

            .modal-overlay {
                padding: 12px;
            }

            .modal-top {
                padding: 18px 18px 14px;
                gap: 10px;
            }

            .modal-top__icon {
                width: 42px;
                height: 42px;
                font-size: 1rem;
                border-radius: 11px;
            }

            .modal-top__title {
                font-size: 1rem;
            }

            .modal-top__type {
                font-size: .76rem;
            }

            .modal-close {
                width: 30px;
                height: 30px;
                font-size: .84rem;
                border-radius: 8px;
            }

            .modal-details {
                padding: 0 18px 14px;
                gap: 9px;
            }

            .detail-row {
                gap: 10px;
            }

            .detail-row__icon {
                width: 32px;
                height: 32px;
                font-size: .78rem;
                border-radius: 8px;
            }

            .detail-row__label {
                font-size: .76rem;
            }

            .detail-row__value {
                font-size: .86rem;
            }

            .modal-stats {
                padding: 0 18px 14px;
                gap: 7px;
            }

            .mini-stat {
                padding: 10px 6px;
                border-radius: 9px;
            }

            .mini-stat__value {
                font-size: 1.05rem;
            }

            .mini-stat__label {
                font-size: .66rem;
            }

            .modal-cta {
                padding: 0 18px 18px;
            }

            .btn-cta {
                height: 42px;
                border-radius: 9px;
                font-size: .84rem;
                gap: 6px;
            }

            .btn-cta i {
                font-size: .76rem;
            }
        }

        @media (max-width: 320px) {
            .stats {
                grid-template-columns: 1fr;
                gap: 6px;
                margin-bottom: 18px;
            }

            .stat {
                padding: 12px;
                gap: 10px;
                border-radius: 10px;
            }

            .stat__icon {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                font-size: .88rem;
            }

            .stat__value {
                font-size: 1.2rem;
            }

            .stat__label {
                font-size: .72rem;
            }

            .sec {
                margin-bottom: 8px;
                gap: 8px;
            }

            .sec__dot {
                width: 22px;
                height: 22px;
                font-size: .74rem;
                border-radius: 6px;
            }

            .sec__text {
                font-size: .82rem;
            }

            .sec__count {
                font-size: .68rem;
            }

            .resources {
                gap: 6px;
                margin-bottom: 18px;
            }

            .res {
                padding: 12px;
                border-radius: 10px;
                gap: 10px;
            }

            .res__icon {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                font-size: .88rem;
            }

            .res__name {
                font-size: .84rem;
            }

            .res__days {
                font-size: .72rem;
            }

            .res__meta {
                gap: 8px;
                margin-top: 4px;
            }

            .res__tag {
                font-size: .66rem;
                padding: 1px 5px;
                border-radius: 4px;
            }

            .res__tag i {
                font-size: .52rem;
            }

            .modal-overlay {
                padding: 10px;
            }

            .modal-box {
                border-radius: 14px;
            }

            .modal-top {
                padding: 16px 16px 12px;
                gap: 10px;
            }

            .modal-top__icon {
                width: 40px;
                height: 40px;
                font-size: .98rem;
                border-radius: 10px;
            }

            .modal-top__title {
                font-size: .98rem;
            }

            .modal-top__type {
                font-size: .74rem;
            }

            .modal-close {
                width: 28px;
                height: 28px;
                font-size: .8rem;
                border-radius: 7px;
            }

            .modal-details {
                padding: 0 16px 12px;
                gap: 8px;
            }

            .detail-row__icon {
                width: 30px;
                height: 30px;
                font-size: .76rem;
                border-radius: 7px;
            }

            .detail-row__label {
                font-size: .74rem;
            }

            .detail-row__value {
                font-size: .84rem;
            }

            .modal-stats {
                padding: 0 16px 12px;
                gap: 6px;
            }

            .mini-stat {
                padding: 8px 4px;
                border-radius: 8px;
            }

            .mini-stat__value {
                font-size: .98rem;
            }

            .mini-stat__label {
                font-size: .64rem;
            }

            .modal-cta {
                padding: 0 16px 16px;
            }

            .btn-cta {
                height: 40px;
                font-size: .82rem;
                border-radius: 8px;
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
            .page-header__sub,
            .modal-overlay {
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

            .stats {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 8px !important;
                margin-bottom: 16px !important;
            }

            .stat {
                background: #fff !important;
                border: 1px solid #ccc !important;
                border-radius: 0 !important;
                padding: 8px 10px !important;
            }

            .stat__icon {
                width: 24px !important;
                height: 24px !important;
                font-size: .7rem !important;
            }

            .stat__value {
                font-size: 12px !important;
                color: #000 !important;
            }

            .stat__label {
                font-size: 7px !important;
                color: #333 !important;
            }

            .resources {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 6px !important;
            }

            .res {
                background: #fff !important;
                border: 1px solid #ccc !important;
                border-radius: 0 !important;
                padding: 6px 8px !important;
                page-break-inside: avoid;
            }

            .res__icon {
                width: 20px !important;
                height: 20px !important;
                font-size: .6rem !important;
            }

            .res__name {
                font-size: 8px !important;
                color: #000 !important;
            }

            .res__days {
                font-size: 6px !important;
            }

            .res__tag {
                font-size: 6px !important;
                padding: 1px 4px !important;
            }

            .sec__text {
                color: #000 !important;
                font-size: 10px !important;
            }
        }
    </style>
@endsection

@section('content')

    <div class="stats">
        <div class="stat">
            <div class="stat__icon stat__icon--blue"><i class="fi fi-rr-calendar-check"></i></div>
            <div class="stat__content">
                <div class="stat__value">{{ $totalReservas }}</div>
                <div class="stat__label">Reservas ativas</div>
            </div>
        </div>
        <div class="stat">
            <div class="stat__icon stat__icon--green"><i class="fi fi-rr-arrows-repeat"></i></div>
            <div class="stat__content">
                <div class="stat__value">{{ $reservasFixas }}</div>
                <div class="stat__label">Reservas fixas</div>
            </div>
        </div>
        <div class="stat">
            <div class="stat__icon stat__icon--amber"><i class="fi fi-rr-calendar-day"></i></div>
            <div class="stat__content">
                <div class="stat__value">{{ $reservasHoje }}</div>
                <div class="stat__label">Para hoje</div>
            </div>
        </div>
        <div class="stat">
            <div class="stat__icon stat__icon--purple"><i class="fi fi-rr-user"></i></div>
            <div class="stat__content">
                <div class="stat__value">{{ $totalClientes }}</div>
                <div class="stat__label">Clientes</div>
            </div>
        </div>
    </div>

    @foreach ($gruposPorTipo as $grupo)
        <div class="sec">
            <div class="sec__dot" style="background:{{ $grupo->tipo->cor }}20;color:{{ $grupo->tipo->cor }}">
                <i class="fi {{ $grupo->tipo->icone }}"></i>
            </div>
            <span class="sec__text">{{ $grupo->tipo->nome }}</span>
            <span class="sec__count">{{ $grupo->areas->count() }}</span>
        </div>
        <div class="resources">
            @foreach ($grupo->areas as $area)
                <div class="res" data-area-id="{{ $area->id }}">
                    <div class="res__icon" style="background:{{ $grupo->tipo->cor }}20;color:{{ $grupo->tipo->cor }}">
                        <i class="fi {{ $grupo->tipo->icone }}"></i>
                    </div>
                    <div class="res__info">
                        <div class="res__name">{{ $area->nome }}</div>
                        <div class="res__meta">
                            <span class="res__days">{{ $areaData[$area->id]['dias'] ?? '' }}</span>
                            <span class="res__tag"><i class="fi fi-rr-calendar-check"></i>
                                {{ $areaData[$area->id]['total'] ?? 0 }}
                                {{ ($areaData[$area->id]['total'] ?? 0) === 1 ? 'reserva' : 'reservas' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="modal-overlay" id="modalDetalhe">
        <div class="modal-box" id="modalBox">
            <div class="modal-top">
                <div class="modal-top__icon" id="mdIcon"></div>
                <div class="modal-top__info">
                    <div class="modal-top__title" id="mdNome"></div>
                    <div class="modal-top__type" id="mdTipo"></div>
                </div>
                <button type="button" class="modal-close"><i class="fi fi-rr-cross-small"></i></button>
            </div>
            <div class="modal-details">
                <div class="detail-row">
                    <div class="detail-row__icon"><i class="fi fi-rr-calendar"></i></div>
                    <div>
                        <div class="detail-row__label">Dias disponíveis</div>
                        <div class="detail-row__value" id="mdDias"></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row__icon"><i class="fi fi-rr-clock"></i></div>
                    <div>
                        <div class="detail-row__label">Modo de reserva</div>
                        <div class="detail-row__value" id="mdModo"></div>
                    </div>
                </div>
            </div>
            <div class="modal-stats">
                <div class="mini-stat mini-stat--accent">
                    <div class="mini-stat__value" id="mdTotal">0</div>
                    <div class="mini-stat__label">Total</div>
                </div>
                <div class="mini-stat mini-stat--success">
                    <div class="mini-stat__value" id="mdFixas">0</div>
                    <div class="mini-stat__label">Fixas</div>
                </div>
                <div class="mini-stat mini-stat--warning">
                    <div class="mini-stat__value" id="mdUnicas">0</div>
                    <div class="mini-stat__label">Únicas</div>
                </div>
                <div class="mini-stat mini-stat--purple">
                    <div class="mini-stat__value" id="mdMensalistas">0</div>
                    <div class="mini-stat__label">Mensalistas</div>
                </div>
            </div>
            <div class="modal-cta">
                <a href="#" class="btn-cta" id="mdLink">Ver grade de reservas <i
                        class="fi fi-rr-angle-right"></i></a>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        (function() {
            'use strict';
            var areas = {{ Js::from($areaData) }};
            var modalAberto = false;
            var $overlay = document.getElementById('modalDetalhe');
            var $icon = document.getElementById('mdIcon');
            var $nome = document.getElementById('mdNome');
            var $tipo = document.getElementById('mdTipo');
            var $dias = document.getElementById('mdDias');
            var $modo = document.getElementById('mdModo');
            var $total = document.getElementById('mdTotal');
            var $fixas = document.getElementById('mdFixas');
            var $unicas = document.getElementById('mdUnicas');
            var $link = document.getElementById('mdLink');

            function abrirDetalhe(id) {
                var a = areas[id];
                if (!a) return;
                $icon.style.background = a.cor + '20';
                $icon.style.color = a.cor;
                $icon.innerHTML = '<i class="fi ' + a.icone + '"></i>';
                $nome.textContent = a.nome;
                $tipo.textContent = a.tipo;
                $dias.textContent = a.dias;
                $modo.textContent = a.modo === 'DIA_INTEIRO' ? 'Dia inteiro' : 'Por horário (' + a.slots + ' slots)';
                $total.textContent = a.total;
                $fixas.textContent = a.fixas;
                $unicas.textContent = a.unicas;
                document.getElementById('mdMensalistas').textContent = a.mensalistas;
                $link.href = a.url;
                $overlay.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                modalAberto = true;
            }

            function fecharDetalhe() {
                $overlay.classList.remove('is-open');
                document.body.style.overflow = '';
                modalAberto = false;
            }

            document.addEventListener('click', function(e) {
                var res = e.target.closest('[data-area-id]');
                if (res) {
                    abrirDetalhe(parseInt(res.dataset.areaId));
                    return;
                }
                if (e.target.closest('.modal-close')) {
                    fecharDetalhe();
                    return;
                }
                if (e.target === $overlay) {
                    fecharDetalhe();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalAberto) fecharDetalhe();
            });
        })();
    </script>
@endsection
