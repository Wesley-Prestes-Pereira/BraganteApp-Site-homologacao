@extends('layouts.error')

@section('code', '419')
@section('grad-from', '#8b5cf6')
@section('grad-to', '#6366f1')

@section('title', 'Sua sessão expirou')
@section('message', 'Faz um tempinho que você não interage com o sistema e sua sessão expirou por segurança. É só
    recarregar a página e fazer login novamente.')

@section('actions')
    <button onclick="location.reload()" class="btn-e btn-e--fill">
        <i class="fi fi-rr-refresh"></i> Recarregar página
    </button>
    <a href="{{ route('login') }}" class="btn-e btn-e--outline">
        <i class="fi fi-rr-sign-in-alt"></i> Ir para o login
    </a>
@endsection
