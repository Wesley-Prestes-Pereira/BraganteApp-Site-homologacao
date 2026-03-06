@extends('layouts.error')

@section('code', '403')
@section('grad-from', '#f59e0b')
@section('grad-to', '#ef4444')

@section('title', 'Acesso restrito')
@section('message', 'Você não tem permissão para acessar essa área. Se acredita que deveria ter acesso, fale com o
    administrador do sistema.')

@section('actions')
    <a href="{{ route('dashboard') }}" class="btn-e btn-e--fill">
        <i class="fi fi-rr-home"></i> Voltar ao início
    </a>
    <button onclick="history.back()" class="btn-e btn-e--outline">
        <i class="fi fi-rr-arrow-left"></i> Página anterior
    </button>
@endsection
