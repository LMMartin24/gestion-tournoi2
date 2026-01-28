<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\SuperTablesController;
use App\Http\Controllers\Admin\SubTableController;
use App\Http\Controllers\Api\FfttController;
use App\Http\Controllers\Admin\TableGeneratorController;

/*
|--------------------------------------------------------------------------
| Public & API Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify-license/{license}', [FfttController::class, 'verify']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Redirection automatique selon le rôle
    Route::get('/dashboard', [DashboardController::class, 'redirectBasedOnRole'])->name('dashboard');

    // --- PROFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- JOUEUR ---
    Route::get('/player/dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
    Route::post('/register/{subTable}', [DashboardController::class, 'register'])->name('player.register');
    Route::delete('/unregister/{subTable}', [DashboardController::class, 'unregister'])->name('player.unregister');

    // --- COACH (Accès restreint au rôle coach ou admin) ---
    Route::middleware(['can:access-coach'])->group(function () {
        Route::get('/coach/dashboard', [CoachController::class, 'index'])->name('coach.dashboard');
        Route::post('/coach/add-student', [CoachController::class, 'addStudent'])->name('coach.add_student');
        Route::post('/coach/register-player', [CoachController::class, 'registerPlayer'])->name('coach.register_player');
        Route::post('/coach/unregister-player', [CoachController::class, 'unregisterPlayer'])->name('coach.unregister_player');
    });

    // --- ADMIN / ORGANISATEUR ---
    // Note : Le middleware 'admin' doit vérifier que user->role est 'admin' ou 'super_admin'
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::resource('tournaments', TournamentController::class);
        
        // Super Tables (Blocs)
        Route::get('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'create'])->name('super_tables.create');
        Route::post('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'store'])->name('super_tables.store');
        Route::delete('super-tables/{superTable}', [SuperTablesController::class, 'destroy'])->name('super_tables.destroy');

        // Sub Tables (Séries)
        Route::get('super-tables/{superTable}/sub-tables/create', [SubTableController::class, 'create'])->name('sub_tables.create');
        Route::post('super-tables/{superTable}/sub-tables', [SubTableController::class, 'store'])->name('sub_tables.store');
        Route::delete('sub-tables/{subTable}', [SubTableController::class, 'destroy'])->name('sub_tables.destroy'); 

        // Exports Juge-Arbitre
        Route::get('tournaments/{tournament}/tables', [TableGeneratorController::class, 'index'])->name('tables.index');
        Route::post('super-tables/{superTable}/generate', [TableGeneratorController::class, 'generate'])->name('tables.generate');
    });
});

require __DIR__.'/auth.php';