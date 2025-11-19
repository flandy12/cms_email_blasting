<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/token', function () {
    return response()->json([
        'token' => encrypt(env('TOKEN_API')),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/email', [UserController::class, 'index'])->name('email');
    Route::post('/email/import', [UserController::class, 'import'])->name('email.import');
    Route::delete('/email/delete', [UserController::class, 'deleteAllEmail'])->name('email.delete');


    Route::get('/template', [UserController::class, 'messageTemplate'])->name('template');
    Route::get('/template/create', [UserController::class, 'messageTemplateStore'])->name('templateCreate');
    Route::post('/template/create', [UserController::class, 'messageTemplateStorePost'])->name('templateStorePost');

    Route::get('/template/{id}/show', [UserController::class, 'messageTemplateView'])->name('templateView');

    Route::get('/log', function () {
        return Inertia::render('log/Master');
    })->name('log');
});
require __DIR__ . '/settings.php';
require __DIR__ . '/api.php';
