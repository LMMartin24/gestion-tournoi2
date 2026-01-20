<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\SuperTablesController;
use App\Http\Controllers\Admin\SubTableController;
use App\Http\Controllers\Api\FfttController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

// Test API FFTT
Route::get('/verify-license/{license}', [FfttController::class, 'verify']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Common & Profile)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // REDIRECTION INTELLIGENTE : 
    // Cette route décide d'envoyer vers le dashboard joueur ou coach
    Route::get('/dashboard', [DashboardController::class, 'redirectBasedOnRole'])->name('dashboard');

    // Gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Player Routes (Dashboard classique)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // La vue du dashboard joueur (utilisée par redirectBasedOnRole)
    Route::get('/player/dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
    
    // Actions d'inscription joueur seul
    Route::post('/register/{subTable}', [DashboardController::class, 'register'])->name('player.register');
    Route::delete('/unregister/{subTable}', [DashboardController::class, 'unregister'])->name('player.unregister');
});

/*
|--------------------------------------------------------------------------
| Coach Routes (Espace Entraîneur)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Page principale du coach
    Route::get('/coach/dashboard', [CoachController::class, 'index'])->name('coach.dashboard');
    
    // Gestion des élèves
    Route::post('/coach/add-student', [CoachController::class, 'addStudent'])->name('coach.add_student');
    
    // Inscriptions groupées par le coach
    Route::post('/coach/register-player', [CoachController::class, 'registerPlayer'])->name('coach.register_player');
    
    // NOUVELLE ROUTE : Désinscription par le coach (avec popup de confirmation)
    Route::post('/coach/unregister-player', [CoachController::class, 'unregisterPlayer'])->name('coach.unregister_player');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by 'admin' middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Gestion des tournois
    Route::resource('tournaments', TournamentController::class);
    
    // Gestion des Super Tableaux (Blocs / Créneaux)
    Route::get('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'create'])->name('super_tables.create');
    Route::post('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'store'])->name('super_tables.store');
    Route::delete('super-tables/{superTable}', [SuperTablesController::class, 'destroy'])->name('super_tables.destroy');

    // Gestion des Sub Tables (Séries)
    Route::get('super-tables/{superTable}/sub-tables/create', [SubTableController::class, 'create'])->name('sub_tables.create');
    Route::post('super-tables/{superTable}/sub-tables', [SubTableController::class, 'store'])->name('sub_tables.store');
    Route::delete('sub-tables/{subTable}', [SubTableController::class, 'destroy'])->name('sub_tables.destroy'); 
});

require __DIR__.'/auth.php';