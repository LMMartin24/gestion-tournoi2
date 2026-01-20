<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\SuperTablesController;
use App\Http\Controllers\Api\FfttController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubTableController;

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
| Authenticated User Routes (Dashboard & Profile)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by 'admin' middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // --- GESTION DES TOURNOIS ---
    // Utilise resource pour index, create, store, edit, update, destroy, show
    Route::resource('tournaments', TournamentController::class);
    
    // --- GESTION DES SUPER TABLEAUX (Blocs / Créneaux) ---
    // Affichage du formulaire pour un tournoi spécifique
    Route::get('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'create'])
        ->name('super_tables.create');

    // Enregistrement d'un Super Tableau
    Route::post('tournaments/{tournament}/super-tables', [SuperTablesController::class, 'store'])
        ->name('tables.store');

    // Suppression d'un Super Tableau
    Route::delete('super-tables/{superTable}', [SuperTablesController::class, 'destroy'])
        ->name('super_tables.destroy');

    // --- GESTION DES SUB TABLES (Séries) ---

    // Formulaire pour ajouter une SubTable à un SuperTable précis

    Route::get('super-tables/{superTable}/sub-tables/create', [SubTableController::class, 'create'])
        ->name('sub_tables.create');

    // Enregistrement de la SubTable
    Route::post('super-tables/{superTable}/sub-tables', [SubTableController::class, 'store'])
        ->name('sub_tables.store');

    Route::delete('sub-tables/{subTable}', [SubTableController::class, 'destroy'])
        ->name('sub_tables.destroy'); 
    });
    
require __DIR__.'/auth.php';