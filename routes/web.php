<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\SellerLoginController;
use App\Http\Controllers\Auth\SellerRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Seller\CollectionController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ProductStockController;
use App\Http\Controllers\Seller\ShopController;
use Illuminate\Support\Facades\Route;

Auth::routes();
// Customer
Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(FrontendController::class)->name('frontend.')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/shop', 'shop')->name('shop');
    Route::get('/product-detail', 'productDetail')->name('product-detail');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/delivery-policy', 'deliveryPolicy')->name('delivery-policy');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/offers', 'offers')->name('offers');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/refund-policy', 'refundPolicy')->name('refund-policy');
    Route::get('/shop-listing', 'shopListing')->name('shop-listing');
    Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');

    Route::get('/cart', 'cart')->name('cart');
});

Route::group(['middleware' => ['CustomerMiddleware']], function () {
    Route::prefix('account')->name('customer.')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/', 'account')->name('account');
            Route::get('/profile', 'profile')->name('profile');

            Route::get('/checkout', 'checkout')->name('checkout');
            Route::get('/order-success', 'orderSuccess')->name('order-success');

            Route::get('/orders', 'orders')->name('orders');
            Route::get('/order-detail', 'orderDetail')->name('order-detail');
            Route::get('/order-tracking', 'orderTracking')->name('order-tracking');

            Route::get('/wishlist', 'wishlist')->name('wishlist');

            Route::get('/addresses', 'addresses')->name('addresses');
        });
    });
});

// Admin
Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
});

Route::group(['middleware' => ['AdminMiddleware']], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::controller(Dashboard::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', 'list')->name('list');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{category}/show', 'show')->name('show');
                Route::get('/{category}/edit', 'edit')->name('edit');
                Route::put('/{category}', 'update')->name('update');
                Route::delete('/{category}', 'destroy')->name('destroy');
            });
        });

    });
});

// Seller
Route::prefix('seller')->name('seller.')->middleware('guest')->group(function () {
    Route::get('auth/{provider}', [SellerLoginController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SellerLoginController::class, 'handleProviderCallback']);

    Route::get('login', [SellerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [SellerLoginController::class, 'login']);
    Route::get('register', [SellerRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [SellerRegisterController::class, 'register']);
});


Route::group(['middleware' => ['SellerMiddleware']], function () {
    Route::prefix('seller')->name('seller.')->group(function () {
        Route::controller(\App\Http\Controllers\Seller\Dashboard::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
        });

        Route::resource('shops', ShopController::class);
        Route::resource('collections', CollectionController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/stock', [ProductStockController::class, 'store'])->name('products.stock.store');
        Route::get('products/{product}/inventory', [ProductStockController::class, 'index'])->name('products.inventory');

        Route::post('logout', [SellerLoginController::class, 'logout'])->name('logout');
    });
});
