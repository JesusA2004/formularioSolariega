<?php

use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QrController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RequestAttachmentController;
use App\Http\Controllers\Public\PublicRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:reportar')->group(function () {
    Route::get('/', [PublicRequestController::class, 'create'])->name('reportar.create');
    Route::post('/', [PublicRequestController::class, 'store'])->name('reportar.store');
});

Route::get('gracias/{folio}', [PublicRequestController::class, 'success'])->name('reportar.exito');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('solicitudes', [AdminRequestController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{solicitud}', [AdminRequestController::class, 'show'])->name('solicitudes.show');
    Route::patch('solicitudes/{solicitud}', [AdminRequestController::class, 'update'])->name('solicitudes.update');
    Route::get('solicitudes/{solicitud}/adjuntos/{adjunto}', [RequestAttachmentController::class, 'download'])->name('solicitudes.adjuntos.download');

    Route::get('reportes', [ReportController::class, 'index'])->name('reportes.index');
    Route::get('reportes/exportar/excel', [ReportController::class, 'exportExcel'])->name('reportes.export.excel');
    Route::get('reportes/exportar/csv', [ReportController::class, 'exportCsv'])->name('reportes.export.csv');
    Route::get('reportes/exportar/pdf', [ReportController::class, 'exportPdf'])->name('reportes.export.pdf');

    Route::get('configuracion/qr', [QrController::class, 'index'])->name('qr.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('usuarios', [AdminUserController::class, 'index'])->name('usuarios.index');
        Route::post('usuarios', [AdminUserController::class, 'store'])->name('usuarios.store');
        Route::put('usuarios/{usuario}', [AdminUserController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{usuario}/estado', [AdminUserController::class, 'toggleActive'])->name('usuarios.toggle');
        Route::delete('usuarios/{usuario}', [AdminUserController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__.'/settings.php';
