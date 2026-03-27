
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonController;
use App\Http\Controllers\FavoriController;


Route::get('/', fn() => view('welcome'))->name('home');

// Authentification

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Permission
Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->middleware(['auth', 'role:admin'])->name('admin.dashboard');
Route::get('/donateur/dashboard', fn() => view('donateur.dashboard'))->middleware(['auth', 'role:donateur'])->name('donateur.dashboard');
Route::get('/beneficiaire/dashboard', fn() => view('beneficiaire.dashboard'))->middleware(['auth', 'role:beneficiaire'])->name('beneficiaire.dashboard');


Route::get('/campagnes', [CampagneController::class, 'index'])->name('campagnes.index');
Route::get('/campagnes/{campagne}', [CampagneController::class, 'show'])->name('campagnes.show');

//  Campagnes (bénéficiaire seulement)
Route::middleware(['auth', 'role:beneficiaire'])->group(function () {
    Route::get('/campagnes/create', [CampagneController::class, 'create'])->name('campagnes.create');
    Route::post('/campagnes', [CampagneController::class, 'store'])->name('campagnes.store');
    Route::get('/campagnes/{campagne}/edit', [CampagneController::class, 'edit'])->name('campagnes.edit');
    Route::put('/campagnes/{campagne}', [CampagneController::class, 'update'])->name('campagnes.update');
    Route::delete('/campagnes/{campagne}', [CampagneController::class, 'destroy'])->name('campagnes.destroy');
});



//  Admin 
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des campagnes
    Route::post('/campagnes/{campagne}/approuver', [AdminController::class, 'approuver'])->name('campagnes.approuver');
    Route::post('/campagnes/{campagne}/rejeter', [AdminController::class, 'rejeter'])->name('campagnes.rejeter');
    Route::delete('/campagnes/{campagne}', [AdminController::class, 'supprimerCampagne'])->name('campagnes.destroy');

    // Gestion des utilisateurs
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/suspendre', [AdminController::class, 'suspendre'])->name('users.suspendre');
    Route::post('/users/{user}/reactiver', [AdminController::class, 'reactiver'])->name('users.reactiver');

    // Transactions
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
});


// ===== Dons (donateur seulement) =====
Route::middleware(['auth', 'role:donateur'])->group(function () {
    Route::post('/dons', [DonController::class, 'store'])->name('dons.store');
    Route::get('/dons/historique', [DonController::class, 'historique'])->name('dons.historique');
});

// ===== Favoris (donateur seulement) =====
Route::middleware(['auth', 'role:donateur'])->prefix('favoris')->name('favoris.')->group(function () {
    Route::get('/', [FavoriController::class, 'index'])->name('index');
    Route::post('/{campagne}', [FavoriController::class, 'store'])->name('store');
    Route::delete('/{campagne}', [FavoriController::class, 'destroy'])->name('destroy');
});
