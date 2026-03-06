@extends('layouts.auth')

@section('title', 'Entrar')

@section('auth-content')
    <div class="card">
        <h2 class="card__heading">Bem-vindo de volta</h2>
        <p class="card__sub">Acesse sua conta para continuar</p>

        <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
            @csrf

            <div class="field-group">
                <label class="field-group__label" for="email">Email</label>
                <div class="input-box">
                    <i class="fi fi-rr-envelope input-box__ico"></i>
                    <input type="email" id="email" name="email" class="input-box__el" value="{{ old('email') }}"
                        placeholder="seu@email.com" autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="field-group">
                <label class="field-group__label" for="password">Senha</label>
                <div class="input-box">
                    <i class="fi fi-rr-lock input-box__ico"></i>
                    <input type="password" id="password" name="password" class="input-box__el"
                        placeholder="Digite sua senha" autocomplete="current-password" required>
                    <button type="button" class="input-box__btn" id="passToggle" tabindex="-1" aria-label="Mostrar senha">
                        <i class="fi fi-rr-eye-crossed" id="passIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn" id="btnSubmit">
                <span class="btn__text">Entrar</span>
                <span class="btn__spinner"></span>
            </button>
        </form>
    </div>
@endsection

@section('auth-scripts')
    <script>
        document.getElementById('passToggle').addEventListener('click', function() {
            var el = document.getElementById('password');
            var ico = document.getElementById('passIcon');
            var show = el.type === 'password';
            el.type = show ? 'text' : 'password';
            ico.className = show ? 'fi fi-rr-eye' : 'fi fi-rr-eye-crossed';
            this.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var email = document.getElementById('email').value.trim();
            var pass = document.getElementById('password').value;

            if (!email || !pass) {
                e.preventDefault();
                SdbToast.warning('Preencha todos os campos');
                return;
            }

            var btn = document.getElementById('btnSubmit');
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    </script>
@endsection
