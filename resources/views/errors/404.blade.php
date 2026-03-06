@extends('layouts.error')

@section('code', '404')
@section('grad-from', '#5b9cf6')
@section('grad-to', '#8b5cf6')

@section('title', 'Eita, essa página não existe!')
@section('message', 'Parece que você se perdeu pelo caminho. A página que procura foi removida, renomeada ou nunca
    existiu. Que tal voltar para o início?')

@section('actions')
    <a href="{{ route('dashboard') }}" class="btn-e btn-e--fill">
        <i class="fi fi-rr-home"></i> Voltar ao início
    </a>
    <button onclick="history.back()" class="btn-e btn-e--outline">
        <i class="fi fi-rr-arrow-left"></i> Página anterior
    </button>
@endsection
