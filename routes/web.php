<?php

use App\Http\Controllers\ConceptController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('domains', DomainController::class)->except(['show']);

    Route::resource('domains.concepts', ConceptController::class);
    Route::patch('domains/{domain}/concepts/{concept}/status', [ConceptController::class, 'updateStatus'])
        ->name('domains.concepts.updateStatus');
    Route::get('domains/{domain}/concepts/archived', [ConceptController::class, 'archived'])
        ->name('domains.concepts.archived');
    Route::patch('domains/{domain}/concepts/{concept}/restore', [ConceptController::class, 'restore'])
        ->name('domains.concepts.restore');
});

require __DIR__.'/auth.php';
