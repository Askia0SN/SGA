<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use Illuminate\View\View;

class ProgrammePublicController extends Controller
{
    public function index(): View
    {
        $programmes = Programme::query()
            ->where('actif', true)
            ->orderByRaw("CASE niveau WHEN 'classe_preparatoire' THEN 1 WHEN 'licence' THEN 2 WHEN 'master' THEN 3 ELSE 4 END")
            ->orderBy('nom')
            ->get();

        return view('programmes.index', compact('programmes'));
    }
}
