<?php

namespace App\Http\Controllers;

use App\Models\DocumentCandidature;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentCandidatureController extends Controller
{
    public function consulter(DocumentCandidature $document): StreamedResponse
    {
        Gate::authorize('view', $document);
        abort_unless(Storage::disk('local')->exists($document->chemin_fichier), 404);

        return Storage::disk('local')->response(
            $document->chemin_fichier,
            $document->nom_original,
            [
                'Content-Type' => $document->type_mime,
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline',
        );
    }
}
