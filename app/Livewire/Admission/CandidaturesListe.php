<?php

namespace App\Livewire\Admission;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Candidatures - SGA EPF')]
class CandidaturesListe extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $recherche = '';

    #[Url(history: true)]
    public string $programme = '';

    #[Url(history: true)]
    public string $statut = '';

    #[Url(history: true)]
    public string $dateDebut = '';

    #[Url(history: true)]
    public string $dateFin = '';

    public function updated(string $propriete): void
    {
        if (in_array($propriete, ['recherche', 'programme', 'statut', 'dateDebut', 'dateFin'], true)) {
            $this->resetPage();
        }
    }

    public function reinitialiserFiltres(): void
    {
        $this->reset(['recherche', 'programme', 'statut', 'dateDebut', 'dateFin']);
        $this->resetPage();
    }

    public function render()
    {
        /** @var User $utilisateur */
        $utilisateur = auth()->user();
        Gate::forUser($utilisateur)->authorize('viewAny', Candidature::class);

        $base = Candidature::query()->visiblePour($utilisateur);
        $requete = (clone $base)
            ->with([
                'candidat',
                'programme.typesDocuments',
                'documents',
            ])
            ->when(trim($this->recherche) !== '', function (Builder $query): void {
                $recherche = '%'.trim($this->recherche).'%';

                $query->where(function (Builder $filtre) use ($recherche): void {
                    $filtre
                        ->where('code_suivi', 'like', $recherche)
                        ->orWhereHas('candidat', function (Builder $candidat) use ($recherche): void {
                            $candidat
                                ->where('prenom', 'like', $recherche)
                                ->orWhere('nom', 'like', $recherche)
                                ->orWhere('email', 'like', $recherche);
                        });
                });
            })
            ->when($this->programme !== '', fn (Builder $query) => $query->where('programme_id', $this->programme))
            ->when($this->statut !== '', fn (Builder $query) => $query->where('statut', $this->statut))
            ->when($this->dateValide($this->dateDebut), fn (Builder $query) => $query->whereDate('soumise_le', '>=', $this->dateDebut))
            ->when($this->dateValide($this->dateFin), fn (Builder $query) => $query->whereDate('soumise_le', '<=', $this->dateFin))
            ->latest('soumise_le')
            ->latest('id');

        return view('livewire.admission.candidatures-liste', [
            'candidatures' => $requete->paginate(15),
            'programmes' => Programme::query()->orderBy('nom')->get(['id', 'nom']),
            'statuts' => StatutCandidature::cases(),
            'indicateurs' => [
                'total' => (clone $base)->count(),
                'nouvelles' => (clone $base)->where('statut', StatutCandidature::Soumise)->count(),
                'en_traitement' => (clone $base)->where('statut', StatutCandidature::EnTraitementAdmission)->count(),
                'jury' => (clone $base)->where('statut', StatutCandidature::TransmiseAuJury)->count(),
            ],
        ]);
    }

    private function dateValide(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }
}
