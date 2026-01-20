<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('home');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/products', [ProductsController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.products.index');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');

    Route::post('products/{product}/toggle', [ProductsController::class, 'toggle'])->name('products.toggle');
    Route::delete('products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
    Route::delete('products/file/{filename}', [ProductsController::class, 'destroyFile'])->name('products.destroyFile');
});


Route::get('/', [AdminProductsController::class, 'home'])->name('home');
Route::get('/productos', [AdminProductsController::class, 'catalog'])->name('products.catalog');

require __DIR__.'/auth.php';
