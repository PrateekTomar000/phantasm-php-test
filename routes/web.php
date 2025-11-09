<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerUser'])->name('register.submit');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'loginUser'])->name('login.submit');

Route::post('/session/sync', [AuthController::class, 'syncSession']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::post('products', [ProductController::class, 'create'])->middleware('auth:api');
Route::get('products', [ProductController::class, 'getAll']);
Route::get('products/{id}', [ProductController::class, 'getById']);

Route::view('/customer/login', 'customer.login');
Route::view('/customer/register', 'customer.register');
Route::view('/customer/dashboard', 'customer.dashboard')->middleware('auth:customer');



Route::get('/customer/products/data', function () {
    return response()->json(Product::all());
});
Route::get('/brands', function () {
    return response()->json(Brand::all());
});
Route::get('/categories', function () {
    return response()->json(Category::all());
});

Route::get('/admin/products/create', function () {
    return view('create');
})->middleware('auth')->name('products.create');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('stores/create', [StoreController::class, 'createForm'])->name('stores.create');
    Route::post('stores', [StoreController::class, 'store'])->name('stores.store');

    // JSON endpoints for DataTables
    Route::get('stores/data', [StoreController::class, 'data']);
    Route::get('stores/search', [StoreController::class, 'search']);
});

 // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/update1/{cart}', [CartController::class, 'update1'])->name('cart.update1');
    Route::post('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');



    Route::get('/cart/session', [CartController::class, 'sessionCart']);
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/place-order', [CartController::class, 'placeOrder'])->name('cart.placeOrder');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index'); // to view orders

    Route::post('/verify-payment', [CartController::class, 'verifyPayment'])->name('verify.payment');

    Route::get('/sucess', [OrderController::class, 'index'])
    ->name('orders.index');



