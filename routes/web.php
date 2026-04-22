<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $previewId = request()->integer('preview');
        $query = auth()->user()->salesPages()->latest();

        if ($previewId > 0) {
            $query->where('id', $previewId);
        }

        return Inertia::render('Dashboard', [
            'previewPage' => $query->first(),
        ]);
    })->name('dashboard');

    Route::get('/sales-pages', [SalesPageController::class, 'index'])->name('sales-pages.index');
    Route::post('/sales-pages/generate', [SalesPageController::class, 'store'])->name('sales-pages.generate');
    Route::get('/sales-pages/{salesPage}', [SalesPageController::class, 'show'])->name('sales-pages.show');
    Route::post('/sales-pages/{salesPage}/regenerate', [SalesPageController::class, 'regenerate'])->name('sales-pages.regenerate');
    Route::delete('/sales-pages/{salesPage}', [SalesPageController::class, 'destroy'])->name('sales-pages.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
