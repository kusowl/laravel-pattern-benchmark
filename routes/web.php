<?php

use App\Http\Controllers\Benchmark\CheckoutActionController;
use App\Http\Controllers\Benchmark\CheckoutServiceController;
use App\Http\Controllers\Benchmark\CheckoutTraitController;
use App\Http\Controllers\Benchmark\MemoryFootprintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Action Pattern ────────────────────────────────────────────────────────
Route::prefix('benchmark/action')->name('benchmark.action.')->group(function () {
    Route::get('/checkout/{orderId}', [CheckoutActionController::class, 'checkout'])->name('checkout');
    Route::get('/tax/{orderId}',      [CheckoutActionController::class, 'tax'])->name('tax');
    Route::get('/loyalty/{orderId}',  [CheckoutActionController::class, 'loyalty'])->name('loyalty');
});

// ── Service Pattern ───────────────────────────────────────────────────────
Route::prefix('benchmark/service')->name('benchmark.service.')->group(function () {
    Route::get('/checkout/{orderId}', [CheckoutServiceController::class, 'checkout'])->name('checkout');
    Route::get('/tax/{orderId}',      [CheckoutServiceController::class, 'tax'])->name('tax');
    Route::get('/loyalty/{orderId}',  [CheckoutServiceController::class, 'loyalty'])->name('loyalty');
});

// ── Trait Pattern ─────────────────────────────────────────────────────────
Route::prefix('benchmark/trait')->name('benchmark.trait.')->group(function () {
    Route::get('/checkout/{orderId}', [CheckoutTraitController::class, 'checkout'])->name('checkout');
    Route::get('/tax/{orderId}',      [CheckoutTraitController::class, 'tax'])->name('tax');
    Route::get('/loyalty/{orderId}',  [CheckoutTraitController::class, 'loyalty'])->name('loyalty');
});
