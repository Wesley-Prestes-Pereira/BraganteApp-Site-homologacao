@extends('layouts.error')

@section('code', '429')
@section('grad-from', '#f59e0b')
@section('grad-to', '#f97316')

@section('title', 'Calma aí!')
@section('message', 'Você fez várias ações em pouco tempo e precisamos de um momento para acompanhar. Espere alguns
    segundos e tente de novo — sem pressa!')

@section('actions')
    <button onclick="history.back()" class="btn-e btn-e--fill">
        <i class="fi fi-rr-arrow-left"></i> Voltar
    </button>
    <a href="{{ route('dashboard') }}" class="btn-e btn-e--outline">
        <i class="fi fi-rr-home"></i> Ir para o início
    </a>
@endsection
