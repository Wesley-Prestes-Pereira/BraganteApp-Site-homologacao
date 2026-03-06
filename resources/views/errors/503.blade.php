@extends('layouts.error')

@section('code', '503')
@section('grad-from', '#6366f1')
@section('grad-to', '#8b5cf6')

@section('title', 'Estamos em manutenção')
@section('message', 'O sistema está passando por uma atualização para ficar ainda melhor. Voltamos em breve — obrigado
    pela paciência!')

@section('actions')
    <button onclick="location.reload()" class="btn-e btn-e--fill">
        <i class="fi fi-rr-refresh"></i> Verificar novamente
    </button>
@endsection
