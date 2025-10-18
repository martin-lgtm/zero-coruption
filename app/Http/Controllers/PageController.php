<?php

namespace App\Http\Controllers;

use Inertia\Inertia; 

class PageController extends Controller
{
    public function about()
    {
        return Inertia::render('About', [
            'title' => 'About Us',
            'description' => 'This is the about section of our corruption visualization site.',
        ]);
    }

    public function report()
    {
        $sectors = [
            'Здравство', 'Општинска администрација', 'Образование',
            'Полиција', 'Судство', 'Друго',
        ];

        $municipalities = [
            [ 'name' => 'Гевгелија'],
            ['name' => 'Богданци'],
            [ 'name' => 'Босилово'],
            ['name' => 'Валандово'],
            [ 'name' => 'Василево'],
            ['name' => 'Дојран'],
            ['name' => 'Конче'],
            [ 'name' => 'Ново Село'],
            ['name' => 'Радовиш'],
            [ 'name' => 'Струмица'],
        ];

        return Inertia::render('Report', [
            'sectors' => $sectors,
            'municipalities' => $municipalities,
        ]);
    }
}
