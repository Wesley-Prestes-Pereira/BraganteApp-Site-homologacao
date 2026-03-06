@extends('layouts.error')

@section('code', '500')
@section('grad-from', '#ef4444')
@section('grad-to', '#dc2626')

@section('title', 'Ops, algo deu errado!')
@section('message', 'Tivemos um problema interno no servidor. Não se preocupe, já estamos cientes e trabalhando para
    resolver. Tente novamente em alguns instantes.')

@section('actions')
    <button onclick="location.reload()" class="btn-e btn-e--fill">
        <i class="fi fi-rr-refresh"></i> Tentar novamente
    </button>
    <a href="/" class="btn-e btn-e--outline">
        <i class="fi fi-rr-home"></i> Ir para o início
    </a>
@endsection
