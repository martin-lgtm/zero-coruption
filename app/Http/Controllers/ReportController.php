<?php

namespace App\Http\Controllers;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    
    public function create()
    {
        $sectors = [
            'Здравство', 'Општинска администрација', 'Образование',
            'Полиција', 'Судство', 'Друго',
        ];

        $municipalities = [
            ['code' => 'MK-801', 'name' => 'Гевгелија'],
            ['code' => 'MK-802', 'name' => 'Богданци'],
            ['code' => 'MK-705', 'name' => 'Битола'],
        ];

        return Inertia::render('Report/Create', [
            'sectors' => $sectors,
            'municipalities' => $municipalities,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'municipality_name' => 'required|string|max:255',
            'sector'            => 'required|string|max:255',
            'description'       => 'nullable|string|max:2000',
            'evidence_url'      => 'nullable|url|max:2048',
        ]);

        Report::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Пријавата е успешно поднесена!');
    }
}
