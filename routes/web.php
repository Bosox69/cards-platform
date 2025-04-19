<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CardPreviewController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Auth::routes();

// Redirection après connexion
Route::get('/home', function() {
    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.dashboard');
})->name('home');

// Routes protégées pour les clients
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    // Dashboard client
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des commandes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/repeat', [OrderController::class, 'repeat'])->name('orders.repeat');
    
    // Gestion du panier
    Route::get('/cart', [OrderController::class, 'showCart'])->name('orders.cart');
    Route::post('/orders/add-to-cart', [OrderController::class, 'addToCart'])->name('orders.add-to-cart');
    Route::post('/orders/remove-from-cart', [OrderController::class, 'removeFromCart'])->name('orders.remove-from-cart');
    
    // Prévisualisation des cartes
    Route::get('/preview/card/{itemId}', [OrderController::class, 'previewCard'])->name('orders.preview');
    Route::get('/orders/{itemId}/edit', [OrderController::class, 'editCartItem'])->name('orders.edit');
});

// Routes pour l'API (utilisé par les composants React)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/departments/{department}/templates', [TemplateController::class, 'getTemplatesForDepartment']);
    Route::post('/preview-card', [CardPreviewController::class, 'generate']);
});

// Routes protégées pour les administrateurs
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::get('/templates', [AdminController::class, 'templates'])->name('templates.index');
    Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    
    // Gestion des templates
    Route::resource('templates', 'App\Http\Controllers\Admin\TemplateController');
});
