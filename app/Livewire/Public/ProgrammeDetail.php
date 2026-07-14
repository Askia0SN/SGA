<?php

namespace App\Livewire\Public;

use App\Models\Programme;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ProgrammeDetail extends Component
{
    public Programme $programme;

    public function mount(Programme $programme): void
    {
        abort_unless($programme->actif, 404);

        $this->programme = $programme->load(['typesDocuments']);
    }

    public function render()
    {
        return view('livewire.public.programme-detail', [
            'ouvert' => $this->programme->estOuvertAuxCandidatures(),
        ])->title($this->programme->nom.' - EPF Africa');
    }
}
