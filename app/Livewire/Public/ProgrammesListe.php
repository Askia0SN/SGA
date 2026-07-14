<?php

namespace App\Livewire\Public;

use App\Models\Programme;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Programmes - EPF Africa')]
class ProgrammesListe extends Component
{
    public function render()
    {
        $programmes = Programme::query()
            ->where('actif', true)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get()
            ->map(function (Programme $programme) {
                $programme->ouvert = $programme->estOuvertAuxCandidatures();

                return $programme;
            });

        return view('livewire.public.programmes-liste', [
            'programmes' => $programmes,
        ]);
    }
}
