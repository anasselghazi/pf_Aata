
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', fn() => view('welcome'))->name('home');


Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->middleware(['auth', 'role:admin'])->name('admin.dashboard');
Route::get('/donateur/dashboard', fn() => view('donateur.dashboard'))->middleware(['auth', 'role:donateur'])->name('donateur.dashboard');
Route::get('/beneficiaire/dashboard', fn() => view('beneficiaire.dashboard'))->middleware(['auth', 'role:beneficiaire'])->name('beneficiaire.dashboard');
