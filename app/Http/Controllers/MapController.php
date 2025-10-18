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
        // Aggregate counts per municipality + sector
        $data = Report::select('municipality_name','sector', DB::raw('COUNT(*) as total'))
            ->groupBy('municipality_name','sector')
            ->get();

        // Map Macedonian names -> ISO codes (Southeast region set you supplied)
        $NAME_TO_CODE = [
            'Гевгелија'  => 'MK-801',
            'Богданци'   => 'MK-802',
            'Босилово'   => 'MK-803',
            'Валандово'  => 'MK-804',
            'Василево'   => 'MK-805',
            'Дојран'     => 'MK-806',
            'Струмица'   => 'MK-807',
            'Конче'      => 'MK-808',
            'Ново Село'  => 'MK-809',
            'Радовиш'    => 'MK-810',
        ];

        // Pull ALL comments (non-empty descriptions) so we can group by sector
        $comments = Report::select('municipality_name','sector','description','created_at')
            ->whereNotNull('description')->where('description','!=','')
            ->orderByDesc('created_at')
            ->get();

        // Group comments by municipality code and sector
        // $commentsByCodeSector[code][sector] = [ {text, city, sector, created_at}, ... ]
        $commentsByCodeSector = [];
        foreach ($comments as $c) {
            $code = $NAME_TO_CODE[$c->municipality_name] ?? null;
            if (!$code) continue;

            $commentsByCodeSector[$code][$c->sector][] = [
                'text'       => $c->description,
                'city'       => $c->municipality_name,
                'sector'     => $c->sector,
                'created_at' => $c->created_at?->toDateTimeString(),
            ];
        }

        // Build the payload for Vue
        // Each municipality becomes:
        // [
        //   'key' => 'MK-807',
        //   'name' => 'Струмица',
        //   'stats' => ['Здравство' => 3, 'Полиција' => 2, ...],
        //   'comments' => ['Здравство' => [..], 'Полиција' => [..], ...],
        //   'comment_count' => N
        // ]
        $municipalities = [];
        foreach ($data as $row) {
            $code = $NAME_TO_CODE[$row->municipality_name] ?? null;
            if (!$code) continue;

            if (!isset($municipalities[$code])) {
                $municipalities[$code] = [
                    'key'           => $code,
                    'name'          => $row->municipality_name,
                    'stats'         => [],
                    'comments'      => $commentsByCodeSector[$code] ?? [],
                    'comment_count' => isset($commentsByCodeSector[$code])
                        ? collect($commentsByCodeSector[$code])->flatten(1)->count()
                        : 0,
                ];
            }
            $municipalities[$code]['stats'][$row->sector] = (int) $row->total;
        }

        return Inertia::render('MapDashboard', [
            'title'          => 'Пријави по категорија',
            'municipalities' => array_values($municipalities),
        ]);
    }



}
