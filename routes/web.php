
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampagneController;


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