<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - Show de Bola Bragante</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-bragante.png') }}">
    <link href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ========================================
           THEME TOKENS
           ======================================== */

        [data-theme="dark"] {
            --page-bg: #070d1c;
            --card-bg: #0e1729;
            --card-border: #1c2a45;
            --card-shadow: 0 12px 48px rgba(0, 0, 0, .4);

            --input-bg: #111e34;
            --input-border: #253a5a;
            --input-border-hover: #345478;
            --input-focus-border: #5b9cf6;
            --input-focus-glow: rgba(91, 156, 246, .2);

            --text-strong: #f0f4f8;
            --text-body: #cad5e2;
            --text-soft: #8a9bb5;
            --text-faint: #586c86;

            --accent: #5b9cf6;
            --accent-hover: #4588e8;
            --accent-active: #3578d8;
            --accent-shadow: 0 4px 16px rgba(91, 156, 246, .3);

            --toggle-hover-bg: rgba(255, 255, 255, .05);

            --orb-a: rgba(91, 156, 246, .06);
            --orb-b: rgba(139, 92, 246, .04);
        }

        [data-theme="light"] {
            --page-bg: #f3f5f9;
            --card-bg: #ffffff;
            --card-border: #dfe4ec;
            --card-shadow: 0 8px 32px rgba(0, 0, 0, .06);

            --input-bg: #f5f7fa;
            --input-border: #cdd5e0;
            --input-border-hover: #aeb9ca;
            --input-focus-border: #4a8ae6;
            --input-focus-glow: rgba(74, 138, 230, .15);

            --text-strong: #111827;
            --text-body: #374151;
            --text-soft: #6b7280;
            --text-faint: #9ca3af;

            --accent: #4a8ae6;
            --accent-hover: #3b79d4;
            --accent-active: #2d67bf;
            --accent-shadow: 0 4px 12px rgba(74, 138, 230, .25);

            --toggle-hover-bg: rgba(0, 0, 0, .04);

            --orb-a: rgba(74, 138, 230, .05);
            --orb-b: rgba(139, 92, 246, .03);
        }

        /* ========================================
           RESET & BASE
           ======================================== */

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background .35s ease, color .35s ease;
        }

        /* ========================================
           PAGE STRUCTURE
           ======================================== */

        .auth-page {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            position: relative;
            overflow: hidden;
        }

        .auth-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-orb--a {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, var(--orb-a) 0%, transparent 70%);
            top: -200px;
            right: -120px;
        }

        .auth-orb--b {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--orb-b) 0%, transparent 70%);
            bottom: -160px;
            left: -100px;
        }

        .auth-wrap {
            width: 100%;
            max-width: 416px;
            position: relative;
            z-index: 1;
            animation: authFadeUp .5s ease-out;
        }

        @keyframes authFadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           BRAND
           ======================================== */

        .brand {
            text-align: center;
            margin-bottom: 36px;
        }

        .brand__logo {
            width: 66px;
            height: 66px;
            border-radius: 18px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .2);
            transition: transform .25s ease;
        }

        .brand__logo:hover {
            transform: scale(1.06) rotate(-2deg);
        }

        .brand__name {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-strong);
            margin-top: 16px;
            letter-spacing: -.02em;
            transition: color .35s ease;
        }

        .brand__tagline {
            font-size: .8rem;
            font-weight: 500;
            color: var(--text-soft);
            margin-top: 4px;
            transition: color .35s ease;
        }

        /* ========================================
           CARD
           ======================================== */

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: var(--card-shadow);
            transition: background .35s ease, border-color .35s ease, box-shadow .35s ease;
        }

        .card__heading {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-strong);
            text-align: center;
            margin-bottom: 6px;
            transition: color .35s ease;
        }

        .card__sub {
            font-size: .86rem;
            color: var(--text-soft);
            text-align: center;
            margin-bottom: 32px;
            line-height: 1.5;
            transition: color .35s ease;
        }

        /* ========================================
           FORM ELEMENTS
           ======================================== */

        .field-group {
            margin-bottom: 22px;
        }

        .field-group__label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-body);
            margin-bottom: 8px;
            transition: color .35s ease;
        }

        .input-box {
            position: relative;
        }

        .input-box__ico {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-soft);
            font-size: 1rem;
            width: 18px;
            height: 18px;
            pointer-events: none;
            transition: color .25s ease;
        }

        .input-box__el {
            width: 100%;
            height: 50px;
            padding: 0 50px 0 44px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-strong);
            font-family: inherit;
            font-size: .9rem;
            font-weight: 500;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease, background .35s ease, color .35s ease;
        }

        .input-box__el::placeholder {
            color: var(--text-faint);
            font-weight: 400;
        }

        .input-box__el:hover {
            border-color: var(--input-border-hover);
        }

        .input-box__el:focus {
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 3.5px var(--input-focus-glow);
        }

        .input-box__el:focus~.input-box__ico {
            color: var(--accent);
        }

        .input-box__btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            border-radius: 8px;
            color: var(--text-soft);
            cursor: pointer;
            font-size: 1rem;
            transition: color .2s ease, background .2s ease;
        }

        .input-box__btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .input-box__btn:hover {
            color: var(--text-strong);
            background: var(--toggle-hover-bg);
        }

        /* ========================================
           PRIMARY BUTTON
           ======================================== */

        .btn {
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
            background: var(--accent);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: inherit;
            font-weight: 700;
            font-size: .93rem;
            cursor: pointer;
            outline: none;
            box-shadow: var(--accent-shadow);
            transition: background .2s ease, transform .12s ease, box-shadow .2s ease;
        }

        .btn:hover {
            background: var(--accent-hover);
        }

        .btn:active {
            background: var(--accent-active);
            transform: scale(.97);
        }

        .btn:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn__spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255, 255, 255, .25);
            border-top-color: #fff;
            border-radius: 50%;
            animation: btnSpin .55s linear infinite;
        }

        .btn.is-loading .btn__text {
            display: none;
        }

        .btn.is-loading .btn__spinner {
            display: inline-block;
        }

        @keyframes btnSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ========================================
           THEME TOGGLE
           ======================================== */

        .theme-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text-soft);
            cursor: pointer;
            font-size: 1.15rem;
            line-height: 1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            transition: all .3s ease;
        }

        .theme-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .theme-btn:hover {
            color: var(--text-strong);
            border-color: var(--input-border-hover);
            transform: scale(1.06);
        }

        .theme-btn:active {
            transform: scale(.95);
        }

        /* ========================================
           FOOTER
           ======================================== */

        .auth-footer {
            text-align: center;
            padding: 18px 20px;
            font-size: .74rem;
            font-weight: 500;
            color: var(--text-soft);
            transition: color .35s ease;
        }

        /* ========================================
           TOAST
           ======================================== */

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 420px;
            padding: 14px 16px;
            border-radius: 14px;
            font-family: inherit;
            font-size: .85rem;
            font-weight: 500;
            line-height: 1.5;
            pointer-events: auto;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .2);
            animation: toastIn .35s ease-out forwards;
            transform-origin: top right;
        }

        .toast.is-leaving {
            animation: toastOut .25s ease-in forwards;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(20px) scale(.96);
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
                transform: translateX(20px) scale(.96);
            }
        }

        .toast__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            flex-shrink: 0;
            font-size: .88rem;
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
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: none;
            border: none;
            cursor: pointer;
            flex-shrink: 0;
            font-size: .75rem;
            transition: background .15s ease, color .15s ease;
        }

        .toast__close i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toast__progress {
            position: absolute;
            bottom: 0;
            left: 16px;
            right: 16px;
            height: 2.5px;
            border-radius: 2px;
            overflow: hidden;
        }

        .toast__progress-bar {
            height: 100%;
            border-radius: 2px;
            animation: toastProgress var(--toast-duration, 4s) linear forwards;
        }

        @keyframes toastProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* --- Toast Error: Dark --- */
        .toast--error {
            background: rgba(40, 15, 15, .94);
            border: 1px solid rgba(248, 113, 113, .18);
            color: #fca5a5;
        }

        .toast--error .toast__icon {
            background: rgba(239, 68, 68, .14);
            color: #f87171;
        }

        .toast--error .toast__close {
            color: rgba(252, 165, 165, .45);
        }

        .toast--error .toast__close:hover {
            background: rgba(252, 165, 165, .1);
            color: #fecaca;
        }

        .toast--error .toast__progress {
            background: rgba(239, 68, 68, .1);
        }

        .toast--error .toast__progress-bar {
            background: #ef4444;
        }

        /* --- Toast Success: Dark --- */
        .toast--success {
            background: rgba(12, 35, 20, .94);
            border: 1px solid rgba(74, 222, 128, .18);
            color: #86efac;
        }

        .toast--success .toast__icon {
            background: rgba(34, 197, 94, .14);
            color: #4ade80;
        }

        .toast--success .toast__close {
            color: rgba(134, 239, 172, .45);
        }

        .toast--success .toast__close:hover {
            background: rgba(134, 239, 172, .1);
            color: #bbf7d0;
        }

        .toast--success .toast__progress {
            background: rgba(34, 197, 94, .1);
        }

        .toast--success .toast__progress-bar {
            background: #22c55e;
        }

        /* --- Toast Warning: Dark --- */
        .toast--warning {
            background: rgba(40, 30, 10, .94);
            border: 1px solid rgba(251, 191, 36, .18);
            color: #fde68a;
        }

        .toast--warning .toast__icon {
            background: rgba(245, 158, 11, .14);
            color: #fbbf24;
        }

        .toast--warning .toast__close {
            color: rgba(253, 230, 138, .45);
        }

        .toast--warning .toast__close:hover {
            background: rgba(253, 230, 138, .1);
            color: #fef3c7;
        }

        .toast--warning .toast__progress {
            background: rgba(245, 158, 11, .1);
        }

        .toast--warning .toast__progress-bar {
            background: #f59e0b;
        }

        /* --- Toast Error: Light --- */
        [data-theme="light"] .toast--error {
            background: rgba(255, 247, 247, .96);
            border-color: rgba(220, 38, 38, .18);
            color: #991b1b;
            box-shadow: 0 8px 32px rgba(220, 38, 38, .1);
        }

        [data-theme="light"] .toast--error .toast__icon {
            background: rgba(220, 38, 38, .1);
            color: #dc2626;
        }

        [data-theme="light"] .toast--error .toast__close {
            color: rgba(153, 27, 27, .35);
        }

        [data-theme="light"] .toast--error .toast__close:hover {
            background: rgba(220, 38, 38, .08);
            color: #7f1d1d;
        }

        [data-theme="light"] .toast--error .toast__progress {
            background: rgba(220, 38, 38, .08);
        }

        [data-theme="light"] .toast--error .toast__progress-bar {
            background: #dc2626;
        }

        /* --- Toast Success: Light --- */
        [data-theme="light"] .toast--success {
            background: rgba(243, 253, 246, .96);
            border-color: rgba(22, 163, 74, .18);
            color: #14532d;
            box-shadow: 0 8px 32px rgba(22, 163, 74, .08);
        }

        [data-theme="light"] .toast--success .toast__icon {
            background: rgba(22, 163, 74, .1);
            color: #16a34a;
        }

        [data-theme="light"] .toast--success .toast__close {
            color: rgba(20, 83, 45, .35);
        }

        [data-theme="light"] .toast--success .toast__close:hover {
            background: rgba(22, 163, 74, .08);
            color: #14532d;
        }

        [data-theme="light"] .toast--success .toast__progress {
            background: rgba(22, 163, 74, .08);
        }

        [data-theme="light"] .toast--success .toast__progress-bar {
            background: #16a34a;
        }

        /* --- Toast Warning: Light --- */
        [data-theme="light"] .toast--warning {
            background: rgba(255, 252, 240, .96);
            border-color: rgba(217, 119, 6, .18);
            color: #78350f;
            box-shadow: 0 8px 32px rgba(217, 119, 6, .08);
        }

        [data-theme="light"] .toast--warning .toast__icon {
            background: rgba(217, 119, 6, .1);
            color: #d97706;
        }

        [data-theme="light"] .toast--warning .toast__close {
            color: rgba(120, 53, 15, .35);
        }

        [data-theme="light"] .toast--warning .toast__close:hover {
            background: rgba(217, 119, 6, .08);
            color: #78350f;
        }

        [data-theme="light"] .toast--warning .toast__progress {
            background: rgba(217, 119, 6, .08);
        }

        [data-theme="light"] .toast--warning .toast__progress-bar {
            background: #d97706;
        }

        /* ========================================
           1440px+
           ======================================== */
        @media (min-width: 1440px) {
            .auth-wrap {
                max-width: 440px;
            }

            .card {
                padding: 40px 36px;
            }

            .brand__logo {
                width: 72px;
                height: 72px;
                border-radius: 20px;
            }

            .brand__name {
                font-size: 1.4rem;
            }
        }

        /* ========================================
           1024px
           ======================================== */
        @media (max-width: 1024px) {
            .auth-page {
                padding: 40px 24px;
            }
        }

        /* ========================================
           768px
           ======================================== */
        @media (max-width: 768px) {
            .auth-page {
                padding: 36px 20px;
            }

            .brand {
                margin-bottom: 30px;
            }

            .brand__logo {
                width: 58px;
                height: 58px;
                border-radius: 15px;
            }

            .brand__name {
                font-size: 1.2rem;
                margin-top: 14px;
            }

            .card {
                padding: 32px 26px;
                border-radius: 18px;
            }

            .theme-btn {
                top: 16px;
                right: 16px;
                width: 40px;
                height: 40px;
                font-size: 1.05rem;
            }
        }

        /* ========================================
           425px
           ======================================== */
        @media (max-width: 425px) {
            .auth-page {
                padding: 24px 16px;
            }

            .brand {
                margin-bottom: 26px;
            }

            .brand__logo {
                width: 52px;
                height: 52px;
                border-radius: 14px;
            }

            .brand__name {
                font-size: 1.12rem;
                margin-top: 12px;
            }

            .brand__tagline {
                font-size: .76rem;
            }

            .card {
                padding: 28px 22px;
                border-radius: 16px;
            }

            .card__heading {
                font-size: 1.06rem;
            }

            .card__sub {
                font-size: .83rem;
                margin-bottom: 26px;
            }

            .input-box__el {
                height: 48px;
                font-size: .88rem;
            }

            .btn {
                height: 48px;
                font-size: .9rem;
                margin-top: 24px;
            }

            .field-group {
                margin-bottom: 18px;
            }

            .theme-btn {
                top: 14px;
                right: 14px;
                width: 38px;
                height: 38px;
                font-size: 1rem;
                border-radius: 10px;
            }

            .toast-container {
                top: 14px;
                right: 14px;
                left: 14px;
            }

            .toast {
                min-width: 0;
                max-width: 100%;
                padding: 12px 14px;
                gap: 10px;
                font-size: .83rem;
                border-radius: 12px;
            }

            .toast__icon {
                width: 28px;
                height: 28px;
                border-radius: 7px;
                font-size: .82rem;
            }

            .toast__close {
                width: 24px;
                height: 24px;
            }

            .toast__progress {
                left: 14px;
                right: 14px;
            }
        }

        /* ========================================
           375px
           ======================================== */
        @media (max-width: 375px) {
            .auth-page {
                padding: 20px 14px;
            }

            .brand {
                margin-bottom: 22px;
            }

            .brand__logo {
                width: 48px;
                height: 48px;
                border-radius: 12px;
            }

            .brand__name {
                font-size: 1.06rem;
                margin-top: 10px;
            }

            .brand__tagline {
                font-size: .74rem;
            }

            .card {
                padding: 24px 20px;
                border-radius: 14px;
            }

            .card__heading {
                font-size: 1rem;
            }

            .card__sub {
                font-size: .8rem;
                margin-bottom: 22px;
            }

            .field-group {
                margin-bottom: 16px;
            }

            .field-group__label {
                font-size: .76rem;
                margin-bottom: 6px;
            }

            .input-box__el {
                height: 46px;
                padding-left: 40px;
                font-size: .86rem;
            }

            .input-box__ico {
                left: 13px;
                font-size: .95rem;
                width: 16px;
                height: 16px;
            }

            .input-box__btn {
                width: 38px;
                height: 38px;
                font-size: .95rem;
            }

            .btn {
                height: 46px;
                font-size: .88rem;
                margin-top: 22px;
            }

            .theme-btn {
                top: 12px;
                right: 12px;
                width: 36px;
                height: 36px;
                font-size: .95rem;
                border-radius: 9px;
            }

            .toast-container {
                top: 12px;
                right: 12px;
                left: 12px;
            }

            .toast {
                padding: 11px 12px;
                gap: 8px;
                font-size: .8rem;
                border-radius: 10px;
            }

            .toast__icon {
                width: 26px;
                height: 26px;
                border-radius: 6px;
                font-size: .78rem;
            }

            .toast__close {
                width: 22px;
                height: 22px;
                font-size: .7rem;
            }
        }

        /* ========================================
           320px
           ======================================== */
        @media (max-width: 320px) {
            .auth-page {
                padding: 16px 12px;
            }

            .brand {
                margin-bottom: 18px;
            }

            .brand__logo {
                width: 42px;
                height: 42px;
                border-radius: 10px;
            }

            .brand__name {
                font-size: .98rem;
                margin-top: 8px;
            }

            .brand__tagline {
                font-size: .7rem;
            }

            .card {
                padding: 20px 16px;
                border-radius: 12px;
            }

            .card__heading {
                font-size: .94rem;
            }

            .card__sub {
                font-size: .78rem;
                margin-bottom: 18px;
            }

            .field-group {
                margin-bottom: 14px;
            }

            .field-group__label {
                font-size: .74rem;
                margin-bottom: 5px;
            }

            .input-box__el {
                height: 44px;
                padding-left: 36px;
                font-size: .84rem;
                border-radius: 10px;
            }

            .input-box__ico {
                left: 11px;
                font-size: .9rem;
                width: 15px;
                height: 15px;
            }

            .input-box__btn {
                width: 36px;
                height: 36px;
                font-size: .9rem;
                right: 4px;
            }

            .btn {
                height: 44px;
                font-size: .86rem;
                border-radius: 10px;
                margin-top: 18px;
            }

            .theme-btn {
                top: 10px;
                right: 10px;
                width: 34px;
                height: 34px;
                font-size: .88rem;
                border-radius: 8px;
            }

            .auth-footer {
                padding: 14px 12px;
                font-size: .68rem;
            }

            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast {
                padding: 10px;
                gap: 8px;
                font-size: .78rem;
            }

            .toast__icon {
                width: 24px;
                height: 24px;
                font-size: .75rem;
            }

            .toast__close {
                width: 22px;
                height: 22px;
                font-size: .68rem;
            }
        }
    </style>

    @yield('auth-styles')

</head>

<body>
    <div class="auth-page">
        <div class="auth-orb auth-orb--a"></div>
        <div class="auth-orb auth-orb--b"></div>

        <button type="button" class="theme-btn" id="themeToggle" title="Alternar tema">
            <i class="fi fi-rr-sun" id="themeIcon"></i>
        </button>

        <div class="auth-wrap">
            <div class="brand">
                <img src="{{ asset('img/logo-bragante.png') }}" alt="Logo Bragante" class="brand__logo">
                <h1 class="brand__name">Show de Bola</h1>
                <div class="brand__tagline">Bragante</div>
            </div>

            @yield('auth-content')
        </div>
    </div>

    <div class="auth-footer">&copy; {{ date('Y') }} Bragante Show de Bola</div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        (function() {
            var saved = localStorage.getItem('sdb-theme');
            var theme = saved || 'dark';
            apply(theme);

            document.getElementById('themeToggle').addEventListener('click', function() {
                var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                apply(next);
                localStorage.setItem('sdb-theme', next);
            });

            function apply(t) {
                document.documentElement.setAttribute('data-theme', t);
                var ico = document.getElementById('themeIcon');
                if (ico) ico.className = t === 'dark' ? 'fi fi-rr-sun' : 'fi fi-rr-moon';
            }
        })();

        var SdbToast = (function() {
            var icons = {
                error: 'fi fi-rr-exclamation',
                success: 'fi fi-rr-check-circle',
                warning: 'fi fi-rr-exclamation'
            };

            function show(type, message, duration) {
                duration = duration || 4500;
                var container = document.getElementById('toastContainer');
                if (!container || !message) return;

                var existing = container.querySelectorAll('.toast:not(.is-leaving)');
                for (var j = 0; j < existing.length; j++) {
                    var body = existing[j].querySelector('.toast__body');
                    if (body && body.textContent === message) return;
                }

                var toast = document.createElement('div');
                toast.className = 'toast toast--' + type;
                toast.style.setProperty('--toast-duration', duration + 'ms');
                toast.innerHTML =
                    '<div class="toast__icon"><i class="' + (icons[type] || icons.error) + '"></i></div>' +
                    '<div class="toast__body">' + message + '</div>' +
                    '<button class="toast__close" type="button"><i class="fi fi-rr-cross-small"></i></button>' +
                    '<div class="toast__progress"><div class="toast__progress-bar"></div></div>';

                container.appendChild(toast);

                var active = container.querySelectorAll('.toast:not(.is-leaving)');
                for (var k = 0; active.length > 5 && k < active.length - 5; k++) {
                    dismiss(active[k]);
                }

                var close = toast.querySelector('.toast__close');
                close.addEventListener('click', function() {
                    dismiss(toast);
                });

                var timer = setTimeout(function() {
                    dismiss(toast);
                }, duration);

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
                error: function(msg, ms) {
                    show('error', msg, ms);
                },
                success: function(msg, ms) {
                    show('success', msg, ms);
                },
                warning: function(msg, ms) {
                    show('warning', msg, ms);
                }
            };
        })();

        @if (session('toast_error'))
            SdbToast.error(@json(session('toast_error')));
        @endif

        @if (session('toast_success'))
            SdbToast.success(@json(session('toast_success')));
        @endif

        @if (session('status'))
            SdbToast.success(@json(session('status')));
        @endif

        @if ($errors->any())
            SdbToast.error(@json($errors->first()));
        @endif
    </script>

    @yield('auth-scripts')
</body>

</html>
