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
}
