<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgrammePublicController;
use App\Http\Controllers\SuiviCandidatureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('accueil');

Route::get('/programmes', [ProgrammePublicController::class, 'index'])
    ->name('programmes.index');

Route::get('/suivi-candidature', [SuiviCandidatureController::class, 'index'])
    ->name('candidatures.suivi');

Route::post('/suivi-candidature', [SuiviCandidatureController::class, 'rechercher'])
    ->name('candidatures.suivi.rechercher');

Route::get('/admission', function () {
    return view('admission.index');
})->name('admission.accueil');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
