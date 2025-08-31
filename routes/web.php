<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Customer\UserAuthController;
use App\Http\Controllers\Customer\UserDashboardController;
use App\Http\Controllers\PushSubscriptionController;

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

Route::get('/', [FrontendController::class,'index'])->name('shop.index');
// Admin auth
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class,'showLogin'])->name('admin.login');
    Route::post('login', [AdminAuthController::class,'login']);
    Route::get('register', [AdminAuthController::class,'showRegister'])->name('admin.register');
    Route::post('register', [AdminAuthController::class,'register']);
    Route::post('logout', [AdminAuthController::class,'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

        Route::resource('products', ProductController::class);
    });
});



// Customer Route
Route::prefix('customer')->group(function () {
    Route::get('login', [UserAuthController::class,'showLogin'])->name('customer.login');
    Route::post('login', [UserAuthController::class,'login']);
    Route::get('register', [UserAuthController::class,'showRegister'])->name('customer.register');
    Route::post('register', [UserAuthController::class,'register']);
    Route::post('logout', [UserAuthController::class,'logout'])->name('customer.logout');

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', function(){ 
            return view('customer.dashboard'); 
        })->name('customer.dashboard');

        Route::post('orders', [UserDashboardController::class,'store'])->name('customer.orders.store');

        Route::post('push/subscribe', [PushSubscriptionController::class,'store'])->name('push.subscribe');
    });
});

Broadcast::routes([
    'middleware' => ['auth:customer,admin'], // allow both guards
]);
// Route::get('/', function () {
//     return view('welcome');
// });
