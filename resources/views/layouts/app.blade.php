<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Show de Bola</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-bragante.png') }}">
    <link href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [data-theme="dark"] {
            --bg: #060b18;
            --nav-bg: rgba(10, 16, 30, .96);
            --nav-border: rgba(255, 255, 255, .06);
            --card: #0c1322;
            --card-border: rgba(255, 255, 255, .06);
            --input-bg: rgba(255, 255, 255, .04);
            --input-border: rgba(255, 255, 255, .08);
            --input-border-h: rgba(255, 255, 255, .14);
            --input-focus: #5b9cf6;
            --input-glow: rgba(91, 156, 246, .18);
            --t1: #eef2f7;
            --t2: #b0bdd0;
            --t3: #6e809a;
            --t4: #44566e;
            --accent: #5b9cf6;
            --accent-h: #4589e6;
            --accent-glow: rgba(91, 156, 246, .10);
            --success: #34d399;
            --danger: #f87171;
            --warning: #fbbf24;
            --purple: #a78bfa;
            --hover: rgba(255, 255, 255, .04);
            --active: rgba(91, 156, 246, .07);
            --overlay: rgba(0, 0, 0, .55);
        }

        [data-theme="light"] {
            --bg: #f4f6fa;
            --nav-bg: rgba(255, 255, 255, .96);
            --nav-border: rgba(0, 0, 0, .07);
            --card: #ffffff;
            --card-border: rgba(0, 0, 0, .06);
            --input-bg: rgba(0, 0, 0, .03);
            --input-border: rgba(0, 0, 0, .10);
            --input-border-h: rgba(0, 0, 0, .18);
            --input-focus: #3b82f6;
            --input-glow: rgba(59, 130, 246, .14);
            --t1: #0f172a;
            --t2: #475569;
            --t3: #78849a;
            --t4: #a0aec0;
            --accent: #3b82f6;
            --accent-h: #2563eb;
            --accent-glow: rgba(59, 130, 246, .07);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --purple: #8b5cf6;
            --hover: rgba(0, 0, 0, .03);
            --active: rgba(59, 130, 246, .06);
            --overlay: rgba(0, 0, 0, .30);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--t2);
            min-height: 100%;
            -webkit-font-smoothing: antialiased;
            transition: background .3s, color .3s;
        }

        .fi {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .topnav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 32px;
            background: var(--nav-bg);
            border-bottom: 1px solid var(--nav-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: background .3s, border-color .3s;
        }

        .topnav__center {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            justify-content: center;
        }

        .topnav__brand {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            flex-shrink: 0;
            margin-right: 20px;
        }

        .topnav__logo {
            width: 34px;
            height: 34px;
            border-radius: 9px;
        }

        .topnav__title {
            font-size: .96rem;
            font-weight: 800;
            color: var(--t1);
            letter-spacing: -.02em;
        }

        .topnav__links {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .topnav__link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 14px;
            border-radius: 9px;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 600;
            color: var(--t3);
            white-space: nowrap;
            transition: all .15s;
        }

        .topnav__link i {
            font-size: .92rem;
        }

        .topnav__link:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .topnav__link.active {
            color: var(--accent);
            background: var(--active);
        }

        .topnav__right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .topnav__icon-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            border: 1px solid var(--card-border);
            background: transparent;
            color: var(--t3);
            cursor: pointer;
            font-size: 1rem;
            transition: all .15s;
        }

        .topnav__icon-btn:hover {
            color: var(--t1);
            background: var(--hover);
        }

        .topnav__user {
            position: relative;
        }

        .topnav__user-btn {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 4px 12px 4px 4px;
            border-radius: 9px;
            border: 1px solid var(--card-border);
            background: transparent;
            cursor: pointer;
            transition: all .15s;
        }

        .topnav__user-btn:hover {
            background: var(--hover);
        }

        .topnav__avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .topnav__uname {
            font-size: .84rem;
            font-weight: 600;
            color: var(--t1);
        }

        .topnav__urole {
            font-size: .68rem;
            font-weight: 500;
            color: var(--t4);
        }

        .topnav__uinfo {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }

        .topnav__user-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 170px;
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, .22);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all .18s;
            z-index: 100;
        }

        .topnav__user.is-open .topnav__user-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .topnav__umenu-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: .86rem;
            font-weight: 500;
            color: var(--t2);
            text-decoration: none;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            font-family: inherit;
            transition: all .12s;
        }

        .topnav__umenu-item i {
            font-size: .88rem;
            color: var(--t3);
        }

        .topnav__umenu-item:hover {
            background: var(--hover);
            color: var(--t1);
        }

        .topnav__umenu-item--danger:hover {
            background: rgba(248, 113, 113, .07);
            color: var(--danger);
        }

        .topnav__umenu-item--danger:hover i {
            color: var(--danger);
        }

        .bottomnav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 64px;
            background: var(--nav-bg);
            border-top: 1px solid var(--nav-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: background .3s, border-color .3s;
        }

        .bottomnav__items {
            display: flex;
            align-items: stretch;
            justify-content: space-around;
            height: 100%;
            list-style: none;
        }

        .bottomnav__item {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bottomnav__link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: var(--t4);
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: color .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .bottomnav__link i {
            font-size: 1.22rem;
        }

        .bottomnav__label {
            font-size: .64rem;
            font-weight: 600;
            opacity: .7;
            transition: opacity .15s;
        }

        .bottomnav__link.active {
            color: var(--accent);
        }

        .bottomnav__link.active .bottomnav__label {
            opacity: 1;
        }

        .bottomnav__link:active {
            transform: scale(.93);
        }

        .page {
            padding-top: 64px;
            min-height: 100vh;
        }

        .page-header {
            padding: 28px 32px 0;
        }

        .page-header__title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--t1);
            letter-spacing: -.02em;
            line-height: 1.2;
        }

        .page-header__sub {
            font-size: .88rem;
            font-weight: 500;
            color: var(--t2);
            margin-top: 3px;
        }

        .page-content {
            padding: 24px 32px 40px;
        }

        .toast-container {
            position: fixed;
            top: 76px;
            right: 18px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }

        .toast {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 280px;
            max-width: 400px;
            padding: 13px 15px;
            border-radius: 12px;
            font-family: inherit;
            font-size: .86rem;
            font-weight: 500;
            line-height: 1.45;
            pointer-events: auto;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 6px 24px rgba(0, 0, 0, .18);
            animation: toastIn .3s ease-out forwards;
            transform-origin: top right;
        }

        .toast.is-leaving {
            animation: toastOut .22s ease-in forwards;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(16px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateX(16px) scale(.97);
            }
        }

        .toast__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            flex-shrink: 0;
            font-size: .82rem;
        }

        .toast__icon i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toast__body {
            flex: 1;
            min-width: 0;
        }

        .toast__close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: none;
            border: none;
            cursor: pointer;
            flex-shrink: 0;
            font-size: .7rem;
            transition: background .12s, color .12s;
        }

        .toast__close i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toast__progress {
            position: absolute;
            bottom: 0;
            left: 14px;
            right: 14px;
            height: 2px;
            border-radius: 2px;
            overflow: hidden;
        }

        .toast__progress-bar {
            height: 100%;
            border-radius: 2px;
            animation: toastProg var(--toast-dur, 4.5s) linear forwards;
        }

        @keyframes toastProg {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .toast--error {
            background: rgba(40, 15, 15, .94);
            border: 1px solid rgba(248, 113, 113, .15);
            color: #fca5a5;
        }

        .toast--success {
            background: rgba(12, 35, 20, .94);
            border: 1px solid rgba(74, 222, 128, .15);
            color: #86efac;
        }

        .toast--warning {
            background: rgba(40, 30, 10, .94);
            border: 1px solid rgba(251, 191, 36, .15);
            color: #fde68a;
        }

        .toast--error .toast__icon {
            background: rgba(239, 68, 68, .12);
            color: #f87171;
        }

        .toast--success .toast__icon {
            background: rgba(34, 197, 94, .12);
            color: #4ade80;
        }

        .toast--warning .toast__icon {
            background: rgba(245, 158, 11, .12);
            color: #fbbf24;
        }

        .toast--error .toast__close {
            color: rgba(252, 165, 165, .35);
        }

        .toast--success .toast__close {
            color: rgba(134, 239, 172, .35);
        }

        .toast--warning .toast__close {
            color: rgba(253, 230, 138, .35);
        }

        .toast--error .toast__close:hover {
            background: rgba(252, 165, 165, .1);
            color: #fecaca;
        }

        .toast--success .toast__close:hover {
            background: rgba(134, 239, 172, .1);
            color: #bbf7d0;
        }

        .toast--warning .toast__close:hover {
            background: rgba(253, 230, 138, .1);
            color: #fef3c7;
        }

        .toast--error .toast__progress {
            background: rgba(239, 68, 68, .08);
        }

        .toast--error .toast__progress-bar {
            background: #ef4444;
        }

        .toast--success .toast__progress {
            background: rgba(34, 197, 94, .08);
        }

        .toast--success .toast__progress-bar {
            background: #22c55e;
        }

        .toast--warning .toast__progress {
            background: rgba(245, 158, 11, .08);
        }

        .toast--warning .toast__progress-bar {
            background: #f59e0b;
        }

        [data-theme="light"] .toast--error {
            background: rgba(255, 247, 247, .97);
            border-color: rgba(220, 38, 38, .14);
            color: #991b1b;
            box-shadow: 0 6px 24px rgba(220, 38, 38, .07);
        }

        [data-theme="light"] .toast--success {
            background: rgba(243, 253, 246, .97);
            border-color: rgba(22, 163, 74, .14);
            color: #14532d;
            box-shadow: 0 6px 24px rgba(22, 163, 74, .05);
        }

        [data-theme="light"] .toast--warning {
            background: rgba(255, 252, 240, .97);
            border-color: rgba(217, 119, 6, .14);
            color: #78350f;
            box-shadow: 0 6px 24px rgba(217, 119, 6, .05);
        }

        [data-theme="light"] .toast--error .toast__icon {
            background: rgba(220, 38, 38, .08);
            color: #dc2626;
        }

        [data-theme="light"] .toast--success .toast__icon {
            background: rgba(22, 163, 74, .08);
            color: #16a34a;
        }

        [data-theme="light"] .toast--warning .toast__icon {
            background: rgba(217, 119, 6, .08);
            color: #d97706;
        }

        [data-theme="light"] .toast--error .toast__close {
            color: rgba(153, 27, 27, .28);
        }

        [data-theme="light"] .toast--success .toast__close {
            color: rgba(20, 83, 45, .28);
        }

        [data-theme="light"] .toast--warning .toast__close {
            color: rgba(120, 53, 15, .28);
        }

        [data-theme="light"] .toast--error .toast__close:hover {
            background: rgba(220, 38, 38, .06);
            color: #7f1d1d;
        }

        [data-theme="light"] .toast--success .toast__close:hover {
            background: rgba(22, 163, 74, .06);
            color: #14532d;
        }

        [data-theme="light"] .toast--warning .toast__close:hover {
            background: rgba(217, 119, 6, .06);
            color: #78350f;
        }

        [data-theme="light"] .toast--error .toast__progress {
            background: rgba(220, 38, 38, .06);
        }

        [data-theme="light"] .toast--error .toast__progress-bar {
            background: #dc2626;
        }

        [data-theme="light"] .toast--success .toast__progress {
            background: rgba(22, 163, 74, .06);
        }

        [data-theme="light"] .toast--success .toast__progress-bar {
            background: #16a34a;
        }

        [data-theme="light"] .toast--warning .toast__progress {
            background: rgba(217, 119, 6, .06);
        }

        [data-theme="light"] .toast--warning .toast__progress-bar {
            background: #d97706;
        }

        @media (min-width: 1440px) {
            .topnav {
                padding: 0 44px;
                height: 68px;
            }

            .topnav__logo {
                width: 36px;
                height: 36px;
            }

            .topnav__title {
                font-size: 1rem;
            }

            .topnav__link {
                height: 40px;
                padding: 0 16px;
                font-size: .9rem;
            }

            .topnav__link i {
                font-size: .96rem;
            }

            .topnav__icon-btn {
                width: 40px;
                height: 40px;
                font-size: 1.06rem;
            }

            .topnav__avatar {
                width: 34px;
                height: 34px;
                font-size: .72rem;
            }

            .topnav__uname {
                font-size: .86rem;
            }

            .topnav__umenu-item {
                font-size: .88rem;
            }

            .page {
                padding-top: 68px;
            }

            .page-header {
                padding: 32px 44px 0;
            }

            .page-header__title {
                font-size: 1.5rem;
            }

            .page-content {
                padding: 28px 44px 48px;
            }

            .toast-container {
                top: 82px;
                right: 22px;
            }
        }

        @media (max-width: 1199px) {
            .topnav {
                padding: 0 20px;
            }

            .topnav__brand {
                margin-right: 12px;
            }

            .topnav__link {
                padding: 0 12px;
                font-size: .86rem;
            }

            .topnav__uinfo {
                display: none;
            }
        }

        @media (max-width: 1023px) {
            .topnav {
                display: none;
            }

            .bottomnav {
                display: block;
            }

            .page {
                padding-top: 0;
                padding-bottom: 76px;
            }

            .page-header {
                padding: 22px 22px 0;
            }

            .page-header__title {
                font-size: 1.3rem;
            }

            .page-content {
                padding: 18px 22px 28px;
            }

            .toast-container {
                top: 14px;
                right: 14px;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 20px 18px 0;
            }

            .page-header__title {
                font-size: 1.22rem;
            }

            .page-header__sub {
                font-size: .86rem;
            }

            .page-content {
                padding: 16px 18px 24px;
            }

            .bottomnav {
                height: 62px;
            }

            .bottomnav__link i {
                font-size: 1.2rem;
            }

            .bottomnav__label {
                font-size: .62rem;
            }
        }

        @media (max-width: 425px) {
            .page-header {
                padding: 16px 16px 0;
            }

            .page-header__title {
                font-size: 1.14rem;
            }

            .page-header__sub {
                font-size: .82rem;
            }

            .page-content {
                padding: 14px 16px 20px;
            }

            .bottomnav {
                height: 60px;
            }

            .bottomnav__link i {
                font-size: 1.16rem;
            }

            .bottomnav__label {
                font-size: .6rem;
            }

            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast {
                min-width: 0;
                max-width: 100%;
                padding: 11px 13px;
                font-size: .84rem;
                border-radius: 10px;
                gap: 9px;
            }

            .toast__icon {
                width: 26px;
                height: 26px;
                font-size: .78rem;
            }

            .toast__close {
                width: 22px;
                height: 22px;
                font-size: .64rem;
            }
        }

        @media (max-width: 375px) {
            .page-header {
                padding: 14px 14px 0;
            }

            .page-header__title {
                font-size: 1.08rem;
            }

            .page-header__sub {
                font-size: .8rem;
            }

            .page-content {
                padding: 12px 14px 18px;
            }

            .bottomnav {
                height: 58px;
            }

            .bottomnav__link i {
                font-size: 1.12rem;
            }

            .bottomnav__label {
                font-size: .58rem;
            }
        }

        @media (max-width: 320px) {
            .page-header {
                padding: 12px 12px 0;
            }

            .page-header__title {
                font-size: 1rem;
            }

            .page-header__sub {
                font-size: .78rem;
            }

            .page-content {
                padding: 10px 12px 16px;
            }

            .bottomnav {
                height: 54px;
            }

            .bottomnav__link i {
                font-size: 1.06rem;
            }

            .bottomnav__label {
                font-size: .54rem;
            }

            .toast {
                padding: 10px 11px;
                font-size: .8rem;
                gap: 8px;
            }

            .toast__icon {
                width: 24px;
                height: 24px;
                font-size: .74rem;
            }

            .toast__close {
                width: 20px;
                height: 20px;
                font-size: .6rem;
            }
        }
    </style>
    @yield('styles')

</head>

<body>

    <nav class="topnav">
        <div class="topnav__center">
            <a href="{{ route('dashboard') }}" class="topnav__brand">
                <img src="{{ asset('img/logo-bragante.png') }}" alt="Bragante" class="topnav__logo">
                <span class="topnav__title">Show de Bola</span>
            </a>
            <div class="topnav__links">
                <a href="{{ route('dashboard') }}"
                    class="topnav__link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fi fi-rr-apps"></i> Dashboard
                </a>
                @can('reservas.ver')
                    <a href="{{ route('reservas.index') }}"
                        class="topnav__link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-calendar"></i> Reservas
                    </a>
                @endcan
                @can('areas.ver')
                    <a href="{{ route('areas.index') }}"
                        class="topnav__link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-marker"></i> Áreas
                    </a>
                @endcan
                @can('clientes.ver')
                    <a href="{{ route('clientes.index') }}"
                        class="topnav__link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-user"></i> Clientes
                    </a>
                @endcan
                @can('financeiro.ver')
                    <a href="{{ route('pagamentos.index') }}"
                        class="topnav__link {{ request()->routeIs('pagamentos.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-coins"></i> Financeiro
                    </a>
                @endcan
                @role('admin')
                    <a href="{{ route('historico.index') }}"
                        class="topnav__link {{ request()->routeIs('historico.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-clock"></i> Histórico
                    </a>
                @endrole
                @can('usuarios.ver')
                    <a href="{{ route('usuarios.index') }}"
                        class="topnav__link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-users"></i> Usuários
                    </a>
                @endcan
            </div>
        </div>
        <div class="topnav__right">
            <button type="button" class="topnav__icon-btn" id="themeToggle" title="Alternar tema">
                <i class="fi fi-rr-sun" id="themeIcon"></i>
            </button>
            <div class="topnav__user" id="ddUser">
                <button type="button" class="topnav__user-btn" onclick="toggleDD('ddUser')">
                    <div class="topnav__avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    <div class="topnav__uinfo">
                        <span class="topnav__uname">{{ auth()->user()->name }}</span>
                        <span class="topnav__urole">{{ auth()->user()->roles->first()?->name ?? 'usuário' }}</span>
                    </div>
                </button>
                <div class="topnav__user-menu">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="topnav__umenu-item topnav__umenu-item--danger">
                            <i class="fi fi-rr-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <nav class="bottomnav">
        <ul class="bottomnav__items">
            <li class="bottomnav__item">
                <a href="{{ route('dashboard') }}"
                    class="bottomnav__link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fi fi-rr-apps"></i><span class="bottomnav__label">Início</span>
                </a>
            </li>
            @can('reservas.ver')
                <li class="bottomnav__item">
                    <a href="{{ route('reservas.index') }}"
                        class="bottomnav__link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-calendar"></i><span class="bottomnav__label">Reservas</span>
                    </a>
                </li>
            @endcan
            @can('areas.ver')
                <li class="bottomnav__item">
                    <a href="{{ route('areas.index') }}"
                        class="bottomnav__link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-marker"></i><span class="bottomnav__label">Áreas</span>
                    </a>
                </li>
            @endcan
            @can('clientes.ver')
                <li class="bottomnav__item">
                    <a href="{{ route('clientes.index') }}"
                        class="bottomnav__link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-user"></i><span class="bottomnav__label">Clientes</span>
                    </a>
                </li>
            @endcan
            @can('financeiro.ver')
                <li class="bottomnav__item">
                    <a href="{{ route('pagamentos.index') }}"
                        class="bottomnav__link {{ request()->routeIs('pagamentos.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-coins"></i><span class="bottomnav__label">Financeiro</span>
                    </a>
                </li>
            @endcan
            @role('admin')
                <li class="bottomnav__item">
                    <a href="{{ route('historico.index') }}"
                        class="bottomnav__link {{ request()->routeIs('historico.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-clock"></i><span class="bottomnav__label">Histórico</span>
                    </a>
                </li>
            @endrole
            @can('usuarios.ver')
                <li class="bottomnav__item">
                    <a href="{{ route('usuarios.index') }}"
                        class="bottomnav__link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="fi fi-rr-users"></i><span class="bottomnav__label">Usuários</span>
                    </a>
                </li>
            @endcan
            <li class="bottomnav__item">
                <button type="button" class="bottomnav__link" onclick="switchTheme()">
                    <i class="fi fi-rr-sun" id="themeIconMobile"></i><span class="bottomnav__label">Tema</span>
                </button>
            </li>
        </ul>
    </nav>

    <main class="page">
        @hasSection('page-header')
            <div class="page-header">@yield('page-header')</div>
        @endif
        <div class="page-content">@yield('content')</div>
    </main>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function fetchApi(url, opts) {
            opts = opts || {};
            opts.headers = Object.assign({
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
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

        (function() {
            applyTheme(localStorage.getItem('sdb-theme') || 'dark');
            var t = document.getElementById('themeToggle');
            if (t) t.addEventListener('click', switchTheme);
        })();

        function switchTheme() {
            var novo = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(novo);
            localStorage.setItem('sdb-theme', novo);
        }

        function applyTheme(tema) {
            document.documentElement.setAttribute('data-theme', tema);
            ['themeIcon', 'themeIconMobile'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.className = tema === 'dark' ? 'fi fi-rr-sun' : 'fi fi-rr-moon';
            });
        }

        function toggleDD(id) {
            var el = document.getElementById(id);
            var aberto = el.classList.contains('is-open');
            closeDD();
            if (!aberto) el.classList.add('is-open');
        }

        function closeDD() {
            document.querySelectorAll('.topnav__user.is-open').forEach(function(el) {
                el.classList.remove('is-open');
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.topnav__user')) closeDD();
        });

        var SdbToast = (function() {
            var icons = {
                error: 'fi fi-rr-exclamation',
                success: 'fi fi-rr-check-circle',
                warning: 'fi fi-rr-exclamation'
            };

            function show(type, msg, dur) {
                dur = dur || 4500;
                var container = document.getElementById('toastContainer');
                if (!container || !msg) return;

                var existentes = container.querySelectorAll('.toast:not(.is-leaving)');
                for (var j = 0; j < existentes.length; j++) {
                    var body = existentes[j].querySelector('.toast__body');
                    if (body && body.textContent === msg) return;
                }

                var toast = document.createElement('div');
                toast.className = 'toast toast--' + type;
                toast.style.setProperty('--toast-dur', dur + 'ms');
                toast.innerHTML =
                    '<div class="toast__icon"><i class="' + (icons[type] || icons.error) + '"></i></div>' +
                    '<div class="toast__body">' + msg + '</div>' +
                    '<button class="toast__close" type="button"><i class="fi fi-rr-cross-small"></i></button>' +
                    '<div class="toast__progress"><div class="toast__progress-bar"></div></div>';

                container.appendChild(toast);

                var active = container.querySelectorAll('.toast:not(.is-leaving)');
                for (var k = 0; active.length > 5 && k < active.length - 5; k++) dismiss(active[k]);

                toast.querySelector('.toast__close').addEventListener('click', function() {
                    dismiss(toast);
                });

                var timer = setTimeout(function() {
                    dismiss(toast);
                }, dur);
                toast.addEventListener('mouseenter', function() {
                    clearTimeout(timer);
                    var bar = toast.querySelector('.toast__progress-bar');
                    if (bar) bar.style.animationPlayState = 'paused';
                });
                toast.addEventListener('mouseleave', function() {
                    var bar = toast.querySelector('.toast__progress-bar');
                    if (bar) bar.style.animationPlayState = 'running';
                    timer = setTimeout(function() {
                        dismiss(toast);
                    }, 1500);
                });
            }

            function dismiss(toast) {
                if (toast.classList.contains('is-leaving')) return;
                toast.classList.add('is-leaving');
                setTimeout(function() {
                    toast.remove();
                }, 250);
            }

            return {
                error: function(msg, dur) {
                    show('error', msg, dur);
                },
                success: function(msg, dur) {
                    show('success', msg, dur);
                },
                warning: function(msg, dur) {
                    show('warning', msg, dur);
                }
            };
        })();
    </script>

    @yield('scripts')
</body>

</html>
