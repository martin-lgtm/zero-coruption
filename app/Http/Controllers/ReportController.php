<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Report;

class ReportController extends Controller
{



public function store(Request $request)
{
    $genderOptions = ['Женски', 'Машки'];
    $feltChoiceOptions = ['Да', 'Не', 'Преферирам да не одговорам'];
    $adminRankOptions = [
        'Машки - висок ранг (раководна или позиција на одговорно лице)',
        'Женски - висок ранг (раководна или позиција на одговорно лице)',
        'Машки - низок ранг (вработен во јавна администрација на секоја друга позиција)',
        'Женски - низок ранг (вработена во јавна администрација на секоја друга позиција)',
        'Преферирам да не одговорам',
    ];
    $ageRangeOptions = ['18-24', '25-34', '35-44', '45-54', '55+'];
    $yesNo = ['Да', 'Не'];
    $wouldReportOptions = ['Да', 'Не', 'Преферирам да не одговорам'];

    $validated = $request->validate([
        'municipality_id'      => ['required', 'integer', 'exists:municipalities,id'],
        'gender'               => ['required', Rule::in($genderOptions)],
        'age_range'            => ['required', Rule::in($ageRangeOptions)],
        'bribe_requested'      => ['required', Rule::in($yesNo)],
        'bribe_offered'        => ['required', Rule::in($yesNo)],
        'would_report_if_safe' => ['required', Rule::in($wouldReportOptions)],
        'felt_choice'          => ['required', Rule::in($feltChoiceOptions)],
        'admin_rank'           => ['required', Rule::in($adminRankOptions)],
        'story'                => ['nullable', 'string', 'max:5000'],

        'sector_ids'           => ['nullable', 'array'],
        'sector_ids.*'         => ['integer', 'exists:sectors,id'],
        'good_ids'             => ['nullable', 'array'],
        'good_ids.*'           => ['integer', 'exists:goods,id'],
        'reason_ids'           => ['nullable', 'array'],
        'reason_ids.*'         => ['integer', 'exists:reasons,id'],

        'consent'              => ['accepted'],
        'consent_publish'      => ['nullable', 'boolean'],
    ]);

    $validated['consent'] = $request->boolean('consent');
    $validated['consent_publish'] = $request->boolean('consent_publish');

    $report = Report::create([
        'municipality_id'      => $validated['municipality_id'],
        'gender'               => $validated['gender'],
        'age_range'            => $validated['age_range'],
        'bribe_requested'      => $validated['bribe_requested'],
        'bribe_offered'        => $validated['bribe_offered'],
        'would_report_if_safe' => $validated['would_report_if_safe'],
        'felt_choice'          => $validated['felt_choice'],
        'admin_rank'           => $validated['admin_rank'],
        'story'                => $validated['story'] ?? null,
        'consent'              => $validated['consent'],          
        'consent_publish'      => $validated['consent_publish'],  
    ]);

    $report->sectors()->sync($validated['sector_ids'] ?? []);
    $report->goods()->sync($validated['good_ids'] ?? []);
    $report->reasons()->sync($validated['reason_ids'] ?? []);

    return redirect()->route('report.create')->with('success', 'Пријавата е успешно поднесена!');
}




}
