<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    return Inertia::render('TestPage', ['message' => 'Hello from Laravel']);
});


Route::get('/map', function () {
    return Inertia::render('MapDashboard', [
        'title' => 'Пријави по категорија',
        'municipalities' => [
            [
                'key' => 'MK-801',
                'name' => 'Гевгелија',
                'stats' => [
                    'Болница' => 14,
                    'Полиција' => 5,
                    'Општина' => 3
                ]
            ],
            [
                'key' => 'MK-705',
                'name' => 'Битола',
                'stats' => [
                    'Болница' => 9,
                    'Полиција' => 2
                ]
            ],
        ],
    ]);
})->name('map');

Route::get('/about', [PageController::class, 'about'])->name('about');



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
