<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\TournamentPublicController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\SuperTablesController;
use App\Http\Controllers\Admin\SubTableController;
use App\Http\Controllers\Admin\TableGeneratorController;
use App\Http\Controllers\Api\FfttController;

// --- ROUTES PUBLIQUES ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tournaments/{slug}', [TournamentPublicController::class, 'show'])->name('tournaments.public.show');
Route::get('/verify-license/{license}', [FfttController::class, 'verify']);

// --- ROUTES AUTHENTIFIÉES ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'redirectBasedOnRole'])->name('dashboard');

    // --- PROFIL ---
    Route::controller(ProfileController::class)->group(function() {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // --- ESPACE JOUEUR ---
    Route::get('/player/dashboard', [DashboardController::class, 'index'])->name('player.dashboard');
    Route::post('/register/{subTable}', [DashboardController::class, 'register'])->name('player.register');
    Route::delete('/unregister/{subTable}', [DashboardController::class, 'unregister'])->name('player.unregister');

    // --- ESPACE COACH ---
    Route::middleware(['can:access-coach'])->prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', [CoachController::class, 'index'])->name('dashboard');
        Route::post('/add-student', [CoachController::class, 'addStudent'])->name('add_student');
        Route::post('/register-player', [CoachController::class, 'registerPlayer'])->name('register_player');
        Route::post('/unregister-player', [CoachController::class, 'unregisterPlayer'])->name('unregister_player');
    });

    // --- ESPACE ADMIN / ORGANISATEUR ---
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Tournois (Gestion par Slug pour les ressources standard)
        Route::resource('tournaments', TournamentController::class)->scoped([
            'tournament' => 'slug',
        ]);

        // Actions spécifiques aux tournois
        Route::patch('/tournaments/{tournament}/approve', [TournamentController::class, 'approve'])->name('tournaments.approve');
        
        // CORRECTION : Route d'export des inscriptions
        // On utilise {id} car l'export se base souvent sur l'ID numérique pour la requête
        Route::get('/tournaments/{id}/export', [TournamentController::class, 'exportRegistrations'])->name('tournaments.export');

        // Super Tables (Blocs horaires)
        Route::post('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'store'])->name('super_tables.store');
        Route::delete('super-tables/{superTable}', [SuperTablesController::class, 'destroy'])->name('super_tables.destroy');

        // Sub Tables (Séries)
        Route::post('super-tables/{superTable}/sub-tables', [SubTableController::class, 'store'])->name('sub_tables.store');
        Route::delete('sub-tables/{subTable}', [SubTableController::class, 'destroy'])->name('sub_tables.destroy'); 

        // Exports & Génération technique
        Route::get('tournaments/{tournament}/tables', [TableGeneratorController::class, 'index'])->name('tables.index');
        Route::post('super-tables/{superTable}/generate', [TableGeneratorController::class, 'generate'])->name('tables.generate');
        Route::get('/subtables/{subTable}/export-girpe', [SubTableController::class, 'exportGirpe'])->name('export.girpe');

        Route::get('/sub-tables/{subTable}/participants', [TournamentController::class, 'viewSubTableParticipants'])
            ->name('sub-tables.participants');
        Route::delete('/registrations/{registration}/cancel', [TournamentController::class, 'cancelRegistration'])
            ->name('registrations.cancel');
    });
});

require __DIR__.'/auth.php';