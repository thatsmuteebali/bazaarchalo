<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Admin\ManagedUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SellerLoginController;
use App\Http\Controllers\Auth\SellerRegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Seller\CollectionController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ProductStockController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\ShopController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['reset' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/create-shop', [EmailVerificationController::class, 'createShop'])->name('create-shop');
    Route::post('/store-shop', [EmailVerificationController::class, 'storeShop'])->name('store-shop');
});

Route::middleware('guest')->group(function () {
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->defaults('role', 'customer')
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->defaults('role', 'customer')
        ->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});
// Customer
Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(FrontendController::class)->name('frontend.')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/shop', 'shop')->name('shop');
    Route::get('/product-detail/{product:slug?}', 'productDetail')->name('product-detail');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'sendContactMessage')->middleware('throttle:5,1')->name('contact.send');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/delivery-policy', 'deliveryPolicy')->name('delivery-policy');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/offers', 'offers')->name('offers');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/refund-policy', 'refundPolicy')->name('refund-policy');
    Route::get('/shop-listing', 'shopListing')->name('shop-listing');
    Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
    Route::get('/seller-support', 'sellerSupport')->name('seller-support');
    Route::get('/seller-guide', 'sellerGuide')->name('seller-guide');
    Route::get('/cart', 'cart')->name('cart');
});

   Route::prefix('cart')->name('cart.')->controller(CartController::class)
       ->middleware('throttle:60,1')->group(function () {
           Route::get('summary', 'summary')->name('summary');
           Route::post('add', 'add')->name('add');
           Route::patch('items/{key}', 'update')->name('update')->where('key', '[A-Za-z0-9\-]+');
           Route::delete('items/{key}', 'remove')->name('remove')->where('key', '[A-Za-z0-9\-]+');
           Route::delete('items', 'clear')->name('clear');
       });

Route::group(['middleware' => ['CustomerMiddleware']], function () {
    Route::prefix('account')->name('customer.')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/', 'account')->name('account');
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');

            Route::get('/checkout', 'checkout')->name('checkout');
            Route::post('/checkout', 'placeOrder')->name('checkout.place');
            Route::get('/order-success', 'orderSuccess')->name('order-success');

            Route::get('/orders', 'orders')->name('orders');
            Route::get('/order-detail', 'orderDetail')->name('order-detail');
            Route::get('/order-tracking', 'orderTracking')->name('order-tracking');

            Route::get('/wishlist', 'wishlist')->name('wishlist');

            Route::get('/addresses', 'addresses')->name('addresses');
        });
        Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', 'index')->defaults('panel', 'customer')->name('index');
            Route::post('/read-all', 'readAll')->defaults('panel', 'customer')->name('read-all');
            Route::post('/{notification}/read', 'read')->defaults('panel', 'customer')->name('read');
        });
    });
});

// Admin
Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->defaults('role', 'admin')
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->defaults('role', 'admin')
        ->name('password.email');
});

Route::group(['middleware' => ['AdminMiddleware']], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::controller(AdminController::class)->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');
        });

        Route::controller(Dashboard::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
        });

        Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', 'index')->defaults('panel', 'admin')->name('index');
            Route::post('/read-all', 'readAll')->defaults('panel', 'admin')->name('read-all');
            Route::post('/{notification}/read', 'read')->defaults('panel', 'admin')->name('read');
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

                Route::patch('/{category}/restore', 'restore')->withTrashed()->name('restore');
                Route::delete('/{category}/force', 'forceDelete')->withTrashed()->name('force-delete');
            });
        });

        Route::controller(PaymentMethodController::class)->prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', 'list')->name('list');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{paymentMethod}/show', 'show')->name('show');
            Route::get('/{paymentMethod}/edit', 'edit')->name('edit');
            Route::put('/{paymentMethod}', 'update')->name('update');
            Route::delete('/{paymentMethod}', 'destroy')->name('destroy');
            Route::patch('/{paymentMethod}/restore', 'restore')->withTrashed()->name('restore');
            Route::delete('/{paymentMethod}/force', 'forceDelete')->withTrashed()->name('force-delete');
        });

        Route::prefix('sellers')->name('sellers.')->controller(ManagedUserController::class)->group(function () {
            Route::get('/', 'sellers')->name('index');
            Route::get('/{userKey}/show', 'showSeller')->name('show');
            Route::get('/{userKey}/edit', 'editSeller')->name('edit');
            Route::put('/{userKey}', 'updateSeller')->name('update');
        });

        Route::prefix('customers')->name('customers.')->controller(ManagedUserController::class)->group(function () {
            Route::get('/', 'customers')->name('index');
            Route::get('/{userKey}/show', 'showCustomer')->name('show');
            Route::get('/{userKey}/edit', 'editCustomer')->name('edit');
            Route::put('/{userKey}', 'updateCustomer')->name('update');
        });

        Route::prefix('riders')->name('riders.')->controller(RiderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{riderKey}/show', 'show')->name('show');
            Route::get('/{riderKey}/edit', 'edit')->name('edit');
            Route::put('/{riderKey}', 'update')->name('update');
            Route::delete('/{riderKey}', 'destroy')->name('destroy');
        });

        Route::prefix('shops')->name('shops.')->controller(AdminShopController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{shop}/show', 'show')->name('show');
            Route::get('/{shop}/edit', 'edit')->name('edit');
            Route::put('/{shop}', 'update')->name('update');
        });

        Route::prefix('collections')->name('collections.')->controller(AdminCollectionController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{collection}/show', 'show')->name('show');
            Route::get('/{collection}/edit', 'edit')->name('edit');
            Route::put('/{collection}', 'update')->name('update');
        });

        Route::prefix('products')->name('products.')->controller(AdminProductController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{product}/show', 'show')->name('show');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::put('/{product}', 'update')->name('update');
        });

        Route::prefix('orders')->name('orders.')->controller(AdminOrderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/{order}/items/{orderItem}/decision', 'decide')
                ->whereNumber('order')
                ->whereNumber('orderItem')
                ->name('items.decide');
            Route::post('/{order}/tracking', 'storeTracking')
                ->whereNumber('order')
                ->name('tracking.store');
            Route::get('/{order}', 'show')->whereNumber('order')->name('show');
        });

        Route::prefix('website')->name('website.')->controller(WebsiteController::class)->group(function () {
            Route::get('/', 'setting')->name('setting');
            Route::post('/', 'store')->name('store');
        });
    });
});

// Seller
Route::prefix('seller')->name('seller.')->middleware('guest')->group(function () {
    Route::get('auth/{provider}', [SellerLoginController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SellerLoginController::class, 'handleProviderCallback']);

    Route::get('login', [SellerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [SellerLoginController::class, 'login']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->defaults('role', 'seller')
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->defaults('role', 'seller')
        ->name('password.email');
    Route::get('register', [SellerRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [SellerRegisterController::class, 'register']);
});


Route::group(['middleware' => ['SellerMiddleware']], function () {
    Route::prefix('seller')->name('seller.')->group(function () {
        Route::controller(\App\Http\Controllers\Seller\Dashboard::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
        });

        Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', 'index')->defaults('panel', 'seller')->name('index');
            Route::post('/read-all', 'readAll')->defaults('panel', 'seller')->name('read-all');
            Route::post('/{notification}/read', 'read')->defaults('panel', 'seller')->name('read');
        });

        Route::controller(SellerController::class)->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');
        });

        Route::resource('shops', ShopController::class);
        Route::patch('shops/{shop}/restore', [ShopController::class, 'restore'])
            ->withTrashed()->name('shops.restore');
        Route::delete('shops/{shop}/force', [ShopController::class, 'forceDelete'])
            ->withTrashed()->name('shops.force-delete');
        Route::resource('collections', CollectionController::class);
        Route::patch('collections/{collection}/restore', [CollectionController::class, 'restore'])
            ->withTrashed()->name('collections.restore');
        Route::delete('collections/{collection}/force', [CollectionController::class, 'forceDelete'])
            ->withTrashed()->name('collections.force-delete');
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
            ->withTrashed()->name('products.restore');
        Route::delete('products/{product}/force', [ProductController::class, 'forceDelete'])
            ->withTrashed()->name('products.force-delete');
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
        Route::patch('order-items/{orderItem}/decision', [OrderController::class, 'decide'])
            ->whereNumber('orderItem')->name('orders.items.decide');
        Route::post('products/{product}/stock', [ProductStockController::class, 'store'])->name('products.stock.store');
        Route::get('products/{product}/inventory', [ProductStockController::class, 'index'])->name('products.inventory');

        Route::post('logout', [SellerLoginController::class, 'logout'])->name('logout');
    });
});
