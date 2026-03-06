<?php

use App\Http\Controllers\{
    AreaController,
    AreaValorController,
    ClienteController,
    DashboardController,
    HistoricoController,
    PagamentoController,
    ReservaController,
    TaxaController,
    TipoAreaController,
    UsuarioController
};
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('historico', [HistoricoController::class, 'index'])->middleware('role:admin')->name('historico.index');

    Route::controller(TipoAreaController::class)->prefix('tipos-area')->middleware('role:admin')->group(function () {
        Route::get('/', 'index')->name('tipos-area.index');
        Route::post('/', 'store')->name('tipos-area.store');
        Route::put('/{id}', 'update')->name('tipos-area.update');
        Route::patch('/{id}/toggle', 'toggleStatus')->name('tipos-area.toggle');
        Route::delete('/{id}', 'destroy')->name('tipos-area.destroy');
        Route::post('/{id}/restore', 'restore')->name('tipos-area.restore');
    });

    Route::controller(AreaController::class)->prefix('areas')->group(function () {
        Route::get('/', 'index')->middleware('permission:areas.ver')->name('areas.index');
        Route::post('/', 'store')->middleware('permission:areas.criar')->name('areas.store');
        Route::put('/{id}', 'update')->middleware('permission:areas.editar')->name('areas.update');
        Route::patch('/{id}/toggle', 'toggleStatus')->middleware('permission:areas.editar')->name('areas.toggle');
        Route::delete('/{id}', 'destroy')->middleware('role:admin')->name('areas.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('areas.restore');
        Route::get('/{id}/horarios', 'horarios')->middleware('permission:areas.ver')->name('areas.horarios');
        Route::post('/{id}/horarios', 'syncHorarios')->middleware('permission:areas.editar')->name('areas.horarios.sync');
        Route::post('/{id}/dias', 'syncDias')->middleware('permission:areas.editar')->name('areas.dias.sync');
    });

    Route::controller(ClienteController::class)->prefix('clientes')->group(function () {
        Route::get('/', 'index')->middleware('permission:clientes.ver')->name('clientes.index');
        Route::get('buscar', 'buscar')->middleware('permission:clientes.ver')->name('clientes.buscar');
        Route::post('/', 'store')->middleware('permission:clientes.criar')->name('clientes.store');
        Route::put('/{id}', 'update')->middleware('permission:clientes.editar')->name('clientes.update');
        Route::patch('/{id}/toggle', 'toggleStatus')->middleware('permission:clientes.editar')->name('clientes.toggle');
        Route::get('exportar/xlsx', 'exportarXlsx')->middleware('permission:clientes.ver')->name('clientes.exportar.xlsx');
        Route::delete('/{id}', 'destroy')->middleware('permission:clientes.excluir')->name('clientes.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('clientes.restore');
    });

    Route::controller(ReservaController::class)->prefix('reservas')->group(function () {
        Route::get('/', 'index')->middleware('permission:reservas.ver')->name('reservas.index');
        Route::post('/', 'store')->middleware('permission:reservas.criar')->name('reservas.store');
        Route::get('data', 'data')->middleware('permission:reservas.ver')->name('reservas.data');
        Route::get('verificar-conflito', 'verificarConflitoApi')->middleware('permission:reservas.ver')->name('reservas.verificar-conflito');
        Route::get('exportar/pdf', 'exportarPdfFiltrado')->middleware('permission:reservas.ver')->name('reservas.exportar.pdf.filtrado');
        Route::get('exportar/xlsx', 'exportarXlsxFiltrado')->middleware('permission:reservas.ver')->name('reservas.exportar.xlsx.filtrado');
        Route::put('/{reserva}', 'update')->middleware('permission:reservas.editar')->name('reservas.update');
        Route::delete('/{reserva}', 'destroy')->middleware('permission:reservas.excluir')->name('reservas.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('reservas.restore');
    });

    Route::controller(TaxaController::class)->prefix('taxas')->group(function () {
        Route::get('/', 'index')->middleware('permission:taxas.ver')->name('taxas.index');
        Route::post('/', 'store')->middleware('permission:taxas.criar')->name('taxas.store');
        Route::put('/{id}', 'update')->middleware('permission:taxas.editar')->name('taxas.update');
        Route::patch('/{id}/toggle', 'toggleStatus')->middleware('permission:taxas.editar')->name('taxas.toggle');
        Route::delete('/{id}', 'destroy')->middleware('permission:taxas.excluir')->name('taxas.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('taxas.restore');
    });

    Route::controller(AreaValorController::class)->prefix('valores')->group(function () {
        Route::get('/', 'index')->middleware('permission:valores.ver')->name('valores.index');
        Route::post('/', 'store')->middleware('permission:valores.criar')->name('valores.store');
        Route::put('/{id}', 'update')->middleware('permission:valores.editar')->name('valores.update');
        Route::patch('/{id}/toggle', 'toggleStatus')->middleware('permission:valores.editar')->name('valores.toggle');
        Route::delete('/{id}', 'destroy')->middleware('permission:valores.excluir')->name('valores.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('valores.restore');
    });

    Route::controller(PagamentoController::class)->prefix('pagamentos')->group(function () {
        Route::get('/', 'index')->middleware('permission:financeiro.ver')->name('pagamentos.index');
        Route::post('/', 'store')->middleware('permission:financeiro.criar')->name('pagamentos.store');
        Route::put('/{id}', 'update')->middleware('permission:financeiro.editar')->name('pagamentos.update');
        Route::get('exportar/xlsx', 'exportarXlsx')->middleware('permission:financeiro.ver')->name('pagamentos.exportar.xlsx');
        Route::delete('/{id}', 'destroy')->middleware('permission:financeiro.excluir')->name('pagamentos.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('pagamentos.restore');
    });

    Route::controller(UsuarioController::class)->prefix('usuarios')->group(function () {
        Route::get('/', 'index')->middleware('permission:usuarios.ver')->name('usuarios.index');
        Route::post('/', 'store')->middleware('permission:usuarios.criar')->name('usuarios.store');
        Route::put('/{usuario}', 'update')->middleware('permission:usuarios.editar')->name('usuarios.update');
        Route::delete('/{usuario}', 'destroy')->middleware('permission:usuarios.excluir')->name('usuarios.destroy');
        Route::post('/{id}/restore', 'restore')->middleware('role:admin')->name('usuarios.restore');
    });
});

require __DIR__ . '/auth.php';
