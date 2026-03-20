<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', [FrontController::class, 'index'])->name('overview');
    // CRUD completo
    Route::resource('statements', StatementController::class);
    
    // Importar PDF
    Route::get('statements-import',  [StatementController::class, 'importForm'])->name('statements.import');
    Route::post('statements-import', [StatementController::class, 'import'])->name('statements.import.process');
    
    // Descargar PDF
    Route::get('statements/{statement}/pdf', [StatementController::class, 'downloadPdf'])->name('statements.pdf');
    
    Route::resource('watchlist', WatchlistController::class);
    Route::patch('watchlist/{watchlist}/toggle-status', [WatchlistController::class, 'toggleStatus'])
        ->name('watchlist.toggle-status');
    
    Route::resource('media', MediaFileController::class);
     
    // Streaming privado (sirve archivos sin exponer storage)
    Route::get('media/{medium}/stream', [MediaFileController::class, 'stream'])->name('media.stream');
    Route::get('media/{medium}/thumbnail', [MediaFileController::class, 'thumbnail'])->name('media.thumbnail');
     
    // Descarga forzada
    Route::get('media/{medium}/download', [MediaFileController::class, 'download'])->name('media.download');
     
    // Subida múltiple
    Route::post('media/bulk-upload', [MediaFileController::class, 'bulkUpload'])->name('media.bulk-upload');
});




Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});
 
// Authenticated
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});