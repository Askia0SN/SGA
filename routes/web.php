<?php

use App\Http\Controllers\DocumentCandidatureController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuiviCandidatureController;
use App\Http\Controllers\TableauBordController;
use App\Http\Controllers\UtilisateurController;
use App\Livewire\Admission\CandidatureDetail;
use App\Livewire\Admission\CandidaturesListe;
use App\Livewire\Public\ConfirmationCandidature;
use App\Livewire\Public\FormulaireCandidature;
use App\Livewire\Public\ProgrammeDetail;
use App\Livewire\Public\ProgrammesListe;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('accueil');

Route::get('/admission', function () {
    return view('admission.index');
})->name('admission.accueil');

Route::get('/programmes', ProgrammesListe::class)->name('programmes.index');
Route::get('/programmes/{programme}', ProgrammeDetail::class)->name('programmes.show');
Route::get('/programmes/{programme}/candidature', FormulaireCandidature::class)->name('candidature.create');
Route::get('/candidature/confirmation/{code}', ConfirmationCandidature::class)->name('candidature.confirmation');

Route::get('/suivi-candidature', [SuiviCandidatureController::class, 'index'])
    ->name('candidatures.suivi');

Route::post('/suivi-candidature', [SuiviCandidatureController::class, 'rechercher'])
    ->name('candidatures.suivi.rechercher');

Route::prefix('admission')
    ->middleware(['auth', 'compte.actif', 'verified', 'role:super_admin,service_admission,jury'])
    ->group(function () {
        Route::get('/tableau-de-bord', [TableauBordController::class, 'index'])->name('dashboard');

        Route::get('/candidatures', CandidaturesListe::class)->name('candidatures.index');
        Route::get('/candidatures/{candidature}', CandidatureDetail::class)->name('candidatures.show');
        Route::get('/documents/{document}/consulter', [DocumentCandidatureController::class, 'consulter'])
            ->name('documents.consulter');

        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware('role:super_admin')->group(function () {
            Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
            Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
            Route::patch('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
            Route::post('/utilisateurs/{utilisateur}/invitation', [UtilisateurController::class, 'renvoyerInvitation'])
                ->name('utilisateurs.invitation');
        });
    });

Route::get('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');

require __DIR__.'/auth.php';
