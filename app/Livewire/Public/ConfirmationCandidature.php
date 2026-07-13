<?php

namespace App\Livewire\Public;

use App\Models\Candidature;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ConfirmationCandidature extends Component
{
    public Candidature $candidature;

    public function mount(string $code): void
    {
        $this->candidature = Candidature::query()
            ->with(['candidat', 'programme'])
            ->where('code_suivi', $code)
            ->whereIn('statut', ['soumise', 'brouillon'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.confirmation-candidature')
            ->title('Confirmation - EPF Africa');
    }
}
