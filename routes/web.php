<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Public\ConfirmationCandidature;
use App\Livewire\Public\FormulaireCandidature;
use App\Livewire\Public\ProgrammeDetail;
use App\Livewire\Public\ProgrammesListe;
use App\Models\Programme;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/programmes', ProgrammesListe::class)->name('programmes.index');
Route::get('/programmes/{programme}', ProgrammeDetail::class)->name('programmes.show');
Route::get('/programmes/{programme}/candidature', FormulaireCandidature::class)->name('candidature.create');
Route::get('/candidature/confirmation/{code}', ConfirmationCandidature::class)->name('candidature.confirmation');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');

require __DIR__.'/auth.php';
