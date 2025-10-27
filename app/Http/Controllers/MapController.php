<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Report;

class MapController extends Controller
{


    public function index()
{
    $NAME_TO_CODE = [
        'Гевгелија' => 'MK-801',
        'Богданци'  => 'MK-802',
        'Босилово'  => 'MK-803',
        'Валандово' => 'MK-804',
        'Василево'  => 'MK-805',
        'Дојран'    => 'MK-806',
        'Струмица'  => 'MK-807',
        'Конче'     => 'MK-808',
        'Ново Село' => 'MK-809',
        'Радовиш'   => 'MK-810',
        'Скопје'    => 'MK-SK',
    ];
    $muniKey = fn ($name) => $NAME_TO_CODE[$name] ?? null;

    $payload = [];
    $totals = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->select('municipalities.name as municipality_name', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name')
        ->get();

    foreach ($totals as $t) {
        $code = $muniKey($t->municipality_name);
        if (!$code) continue;
        $payload[$code] = [
            'key' => $code,
            'name' => $t->municipality_name,
            'total' => (int) $t->total,
            'charts' => [
                'sectors' => [],
                'goods' => [],
                'reasons' => [],
                'ages' => [],
                'bribe_requested' => [],
                'bribe_offered' => [],
                'would_report'=>[],
            ],
            'comments' => [],
        ];
    }

    $attachCounts = function (&$bag, $rows, $bucket, $muniKeyCb) {
        foreach ($rows as $r) {
            $code = $muniKeyCb($r->municipality_name);
            if (!$code) continue;
            if (!isset($bag[$code])) {
                $bag[$code] = [
                    'key' => $code,
                    'name' => $r->municipality_name,
                    'total' => 0,
                    'charts' => [
                        'sectors' => [], 'goods' => [], 'reasons' => [],
                        'ages' => [], 'bribe_requested' => [], 'bribe_offered' => [],
                        'would_report'=>[],
                    ],
                    'comments' => [],
                ];
            }
            $bag[$code]['charts'][$bucket][$r->label] = (int) $r->total;
        }
    };

    // 1️⃣ Sectors
    $bySectors = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->join('report_sector', 'reports.id', '=', 'report_sector.report_id')
        ->join('sectors', 'report_sector.sector_id', '=', 'sectors.id')
        ->select('municipalities.name as municipality_name', 'sectors.name as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'sectors.name')
        ->get();
    $attachCounts($payload, $bySectors, 'sectors', $muniKey);

    // 2️⃣ Goods
    $byGoods = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->join('report_good', 'reports.id', '=', 'report_good.report_id')
        ->join('goods', 'report_good.good_id', '=', 'goods.id')
        ->select('municipalities.name as municipality_name', 'goods.name as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'goods.name')
        ->get();
    $attachCounts($payload, $byGoods, 'goods', $muniKey);

    // 3️⃣ Reasons
    $byReasons = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->join('report_reason', 'reports.id', '=', 'report_reason.report_id')
        ->join('reasons', 'report_reason.reason_id', '=', 'reasons.id')
        ->select('municipalities.name as municipality_name', 'reasons.name as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'reasons.name')
        ->get();
    $attachCounts($payload, $byReasons, 'reasons', $muniKey);

    // 4️⃣ Ages
    $byAges = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->whereNotNull('reports.age_range')->where('reports.age_range', '!=', '')
        ->select('municipalities.name as municipality_name', 'reports.age_range as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'reports.age_range')
        ->get();
    $attachCounts($payload, $byAges, 'ages', $muniKey);

    // 5️⃣ Bribe requested
    $byRequested = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->whereNotNull('reports.bribe_requested')->where('reports.bribe_requested', '!=', '')
        ->select('municipalities.name as municipality_name', 'reports.bribe_requested as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'reports.bribe_requested')
        ->get();
    $attachCounts($payload, $byRequested, 'bribe_requested', $muniKey);

    // 6️⃣ Bribe offered
    $byOffered = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->whereNotNull('reports.bribe_offered')->where('reports.bribe_offered', '!=', '')
        ->select('municipalities.name as municipality_name', 'reports.bribe_offered as label', DB::raw('COUNT(*) as total'))
        ->groupBy('municipalities.name', 'reports.bribe_offered')
        ->get();
    $attachCounts($payload, $byOffered, 'bribe_offered', $muniKey);

    // 7️⃣ Would report if safe
$byWouldReport = DB::table('reports')
    ->join('municipalities','reports.municipality_id','=','municipalities.id')
    ->whereNotNull('reports.would_report_if_safe')->where('reports.would_report_if_safe','!=','')
    ->select(
        'municipalities.name as municipality_name',
        'would_report_if_safe as label',
        DB::raw('COUNT(*) as total')
    )
    ->groupBy('municipalities.name','would_report_if_safe')
    ->get();

$attachCounts($payload, $byWouldReport, 'would_report', $muniKey);


    // 💬 Comments
    $comments = DB::table('reports')
        ->join('municipalities', 'reports.municipality_id', '=', 'municipalities.id')
        ->whereNotNull('reports.story')->where('reports.story', '!=', '')
        ->select('reports.id as report_id', 'municipalities.name as municipality_name', 'reports.story', 'reports.created_at')
        ->orderByDesc('reports.created_at')
        ->get();

    foreach ($comments as $c) {
        $code = $muniKey($c->municipality_name);
        if (!$code) continue;
        $payload[$code]['comments'][] = [
            'text' => $c->story,
            'created_at' => (string) $c->created_at,
        ];
    }

    return Inertia::render('MapDashboard', [
        'title' => 'Пријави по категорија',
        'municipalities' => array_values($payload),
    ]);
}



}
