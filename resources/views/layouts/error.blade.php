<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('code', 'Erro') - Show de Bola Bragante</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-bragante.png') }}">
    <link href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [data-theme="dark"] {
            --page-bg: #070d1c;
            --text-strong: #f0f4f8;
            --text-body: #cad5e2;
            --text-soft: #8a9bb5;
            --text-faint: #586c86;
            --accent: #5b9cf6;
            --accent-hover: #4588e8;
            --accent-shadow: 0 4px 16px rgba(91, 156, 246, .3);
            --ghost-bg: rgba(255, 255, 255, .04);
            --ghost-border: #1c2a45;
            --ghost-hover-bg: rgba(255, 255, 255, .07);
            --ghost-hover-border: #2a3d5e;
            --toggle-bg: #0e1729;
            --toggle-border: #1c2a45;
            --toggle-color: #8a9bb5;
            --orb-a: rgba(91, 156, 246, .06);
            --orb-b: rgba(139, 92, 246, .04);
        }

        [data-theme="light"] {
            --page-bg: #f3f5f9;
            --text-strong: #111827;
            --text-body: #374151;
            --text-soft: #6b7280;
            --text-faint: #9ca3af;
            --accent: #4a8ae6;
            --accent-hover: #3b79d4;
            --accent-shadow: 0 4px 12px rgba(74, 138, 230, .2);
            --ghost-bg: rgba(0, 0, 0, .03);
            --ghost-border: #e2e8f0;
            --ghost-hover-bg: rgba(0, 0, 0, .05);
            --ghost-hover-border: #cbd5e0;
            --toggle-bg: #ffffff;
            --toggle-border: #e2e8f0;
            --toggle-color: #6b7280;
            --orb-a: rgba(74, 138, 230, .05);
            --orb-b: rgba(139, 92, 246, .03);
        }

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
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            position: relative;
            transition: background .35s ease, color .35s ease;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .orb--a {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, var(--orb-a) 0%, transparent 70%);
            top: -200px;
            right: -120px;
        }

        .orb--b {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--orb-b) 0%, transparent 70%);
            bottom: -160px;
            left: -100px;
        }

        .theme-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--toggle-bg);
            border: 1px solid var(--toggle-border);
            border-radius: 12px;
            color: var(--toggle-color);
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
            transform: scale(1.06);
        }

        .theme-btn:active {
            transform: scale(.95);
        }

        .error-wrap {
            text-align: center;
            padding: 40px 24px;
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: fadeUp .5s ease-out;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-logo {
            width: 88px;
            height: 88px;
            border-radius: 22px;
            box-shadow: 0 6px 28px rgba(0, 0, 0, .2);
            margin-bottom: 28px;
            transition: transform .25s ease;
        }

        .error-logo:hover {
            transform: scale(1.05);
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1;
            margin-bottom: 14px;

            background: linear-gradient(135deg, @yield('grad-from', '#5b9cf6')

                , @yield('grad-to', '#8b5cf6'));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-strong);
            margin-bottom: 10px;
            transition: color .35s ease;
        }

        .error-msg {
            font-size: .94rem;
            color: var(--text-soft);
            line-height: 1.65;
            margin-bottom: 32px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            transition: color .35s ease;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-e {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 46px;
            padding: 0 24px;
            border-radius: 12px;
            font-family: inherit;
            font-size: .88rem;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            border: none;
            outline: none;
            white-space: nowrap;
        }

        .btn-e i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            width: 16px;
            height: 16px;
        }

        .btn-e--fill {
            background: var(--accent);
            color: #fff;
            box-shadow: var(--accent-shadow);
        }

        .btn-e--fill:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-e--fill:active {
            transform: translateY(0) scale(.97);
        }

        .btn-e--outline {
            background: var(--ghost-bg);
            color: var(--text-body);
            border: 1px solid var(--ghost-border);
        }

        .btn-e--outline:hover {
            background: var(--ghost-hover-bg);
            border-color: var(--ghost-hover-border);
            color: var(--text-strong);
        }

        .error-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: .74rem;
            font-weight: 500;
            color: var(--text-faint);
            transition: color .35s ease;
        }

        /* 1440px+ */
        @media (min-width: 1440px) {
            .error-wrap {
                max-width: 540px;
            }

            .error-logo {
                width: 96px;
                height: 96px;
                border-radius: 24px;
            }

            .error-code {
                font-size: 5.5rem;
            }

            .error-title {
                font-size: 1.45rem;
            }

            .error-msg {
                font-size: .96rem;
                max-width: 420px;
            }
        }

        /* 1024px */
        @media (max-width: 1024px) {
            .error-wrap {
                padding: 36px 24px;
            }

            .error-logo {
                width: 84px;
                height: 84px;
                border-radius: 20px;
                margin-bottom: 26px;
            }
        }

        /* 768px */
        @media (max-width: 768px) {
            .error-wrap {
                padding: 32px 20px;
            }

            .error-logo {
                width: 76px;
                height: 76px;
                border-radius: 18px;
                margin-bottom: 24px;
            }

            .error-code {
                font-size: 4rem;
                margin-bottom: 12px;
            }

            .error-title {
                font-size: 1.2rem;
                margin-bottom: 10px;
            }

            .error-msg {
                font-size: .88rem;
                margin-bottom: 28px;
            }

            .btn-e {
                height: 44px;
                padding: 0 22px;
                font-size: .86rem;
            }

            .theme-btn {
                top: 16px;
                right: 16px;
                width: 40px;
                height: 40px;
                font-size: 1.05rem;
            }
        }

        /* 425px */
        @media (max-width: 425px) {
            .error-wrap {
                padding: 24px 18px;
            }

            .error-logo {
                width: 68px;
                height: 68px;
                border-radius: 16px;
                margin-bottom: 22px;
            }

            .error-code {
                font-size: 3.4rem;
                margin-bottom: 10px;
            }

            .error-title {
                font-size: 1.1rem;
                margin-bottom: 8px;
            }

            .error-msg {
                font-size: .84rem;
                margin-bottom: 24px;
                max-width: 320px;
            }

            .btn-e {
                height: 42px;
                padding: 0 20px;
                font-size: .83rem;
                border-radius: 10px;
            }

            .error-actions {
                gap: 10px;
            }

            .theme-btn {
                top: 14px;
                right: 14px;
                width: 38px;
                height: 38px;
                font-size: 1rem;
                border-radius: 10px;
            }
        }

        /* 375px */
        @media (max-width: 375px) {
            .error-wrap {
                padding: 22px 16px;
            }

            .error-logo {
                width: 60px;
                height: 60px;
                border-radius: 14px;
                margin-bottom: 20px;
            }

            .error-code {
                font-size: 3rem;
            }

            .error-title {
                font-size: 1.02rem;
            }

            .error-msg {
                font-size: .82rem;
                margin-bottom: 22px;
                max-width: 290px;
                line-height: 1.55;
            }

            .btn-e {
                height: 40px;
                padding: 0 18px;
                font-size: .82rem;
                gap: 6px;
            }

            .btn-e i {
                font-size: .88rem;
                width: 14px;
                height: 14px;
            }

            .error-actions {
                gap: 8px;
            }

            .theme-btn {
                top: 12px;
                right: 12px;
                width: 36px;
                height: 36px;
                font-size: .95rem;
                border-radius: 9px;
            }

            .error-footer {
                bottom: 14px;
                font-size: .7rem;
            }
        }

        /* 320px */
        @media (max-width: 320px) {
            .error-wrap {
                padding: 18px 14px;
            }

            .error-logo {
                width: 52px;
                height: 52px;
                border-radius: 12px;
                margin-bottom: 16px;
            }

            .error-code {
                font-size: 2.4rem;
                margin-bottom: 8px;
            }

            .error-title {
                font-size: .94rem;
                margin-bottom: 6px;
            }

            .error-msg {
                font-size: .78rem;
                margin-bottom: 20px;
                max-width: 260px;
            }

            .btn-e {
                height: 40px;
                padding: 0 16px;
                font-size: .8rem;
                border-radius: 9px;
                width: 100%;
            }

            .btn-e i {
                font-size: .85rem;
                width: 14px;
                height: 14px;
            }

            .error-actions {
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }

            .theme-btn {
                top: 10px;
                right: 10px;
                width: 34px;
                height: 34px;
                font-size: .88rem;
                border-radius: 8px;
            }

            .error-footer {
                bottom: 10px;
                font-size: .66rem;
            }
        }
    </style>
</head>

<body>
    <button type="button" class="theme-btn" id="themeToggle" title="Alternar tema">
        <i class="fi fi-rr-sun" id="themeIcon"></i>
    </button>

    <div class="error-wrap">
        <img src="{{ asset('img/logo-bragante.png') }}" alt="Bragante" class="error-logo">

        <div class="error-code">@yield('code')</div>
        <h1 class="error-title">@yield('title')</h1>
        <p class="error-msg">@yield('message')</p>

        <div class="error-actions">
            @yield('actions')
        </div>
    </div>

    <div class="error-footer">&copy; 2026 Bragante Show de Bola</div>

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
    </script>
</body>

</html>
