<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminMediaController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCampaignController;
use App\Http\Controllers\Admin\AdminPromoBannerController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AdminGiftCardController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminTagController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
// Auth va antes que las públicas para que el login esté disponible en mantenimiento
require __DIR__ . '/auth.php';

// ─── Públicas (con middleware de mantenimiento) ────────────────────────────────
Route::middleware('maintenance')->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Productos
    Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/api/search', [ProductController::class, 'search'])->name('products.search');

    // Páginas estáticas
    Route::get('/quienes-somos', [PageController::class, 'aboutUs'])->name('about');
    Route::get('/preguntas-frecuentes', [PageController::class, 'faq'])->name('faq');
    Route::get('/terminos-y-condiciones', [PageController::class, 'terms'])->name('terms');
    Route::get('/politicas-de-privacidad', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/gift-cards', [PageController::class, 'giftCards'])->name('gift-cards');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.shipping');
    Route::get('/checkout/confirmacion/{orderId}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Mi Cuenta (también bloqueada en mantenimiento para usuarios no admin)
    Route::middleware(['auth', 'verified'])->prefix('mi-cuenta')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/pedidos', [AccountController::class, 'orders'])->name('orders');
        Route::get('/pedidos/{orderNumber}', [AccountController::class, 'orderShow'])->name('order.show');
        Route::get('/direcciones', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/direcciones', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::delete('/direcciones/{id}', [AccountController::class, 'deleteAddress'])->name('addresses.delete');
        Route::get('/lista-de-deseos', [AccountController::class, 'wishlist'])->name('wishlist');
        Route::post('/wishlist/{productId}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');
        Route::patch('/perfil', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/configuracion', [ProfileController::class, 'edit'])->name('profile.edit');
    });

    Route::middleware(['auth', 'verified'])->prefix('mi-cuenta')->group(function () {
        Route::patch('/configuracion', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/configuracion', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});

// Verificación newsletter (pública, throttled)
Route::get('/newsletter/verificar/{token}', [NewsletterController::class, 'verify'])
    ->name('newsletter.verify')
    ->middleware('throttle:10,1');

// Webhook Bancard (público, sin CSRF, sin mantenimiento)
Route::post('/webhooks/bancard', [CheckoutController::class, 'bancardWebhook'])
    ->name('webhooks.bancard')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,vendedor', 'admin.timeout', 'admin.audit'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/home', [AdminController::class, 'home'])->name('home');

    // Perfil y seguridad del admin
    Route::get('mi-perfil', [AdminProfileController::class, 'edit'])->name('profile');
    Route::patch('mi-perfil', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('mi-perfil/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Usuarios (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('usuarios', AdminUserController::class)->except(['show']);
    });

    // Productos
    Route::post('productos/bulk',           [AdminProductController::class, 'bulkAction'])->name('productos.bulk');
    Route::post('productos/import',         [AdminProductController::class, 'import'])->name('productos.import');
    Route::get('productos/export',          [AdminProductController::class, 'export'])->name('productos.export');
    Route::get('productos/export/excel',    [AdminProductController::class, 'exportExcel'])->name('productos.export.excel');
    Route::get('productos/template/csv',    [AdminProductController::class, 'template'])->name('productos.template');
    Route::get('productos/template/excel',  [AdminProductController::class, 'templateExcel'])->name('productos.template.excel');
    Route::resource('productos', AdminProductController::class)->except(['show']);

    // Categorías
    Route::post('categorias',          [AdminCategoryController::class, 'store'])->name('categorias.store');
    Route::patch('categorias/{id}',    [AdminCategoryController::class, 'update'])->name('categorias.update');
    Route::delete('categorias/{id}',   [AdminCategoryController::class, 'destroy'])->name('categorias.destroy');

    // Marcas
    Route::post('marcas',              [AdminBrandController::class, 'store'])->name('marcas.store');
    Route::patch('marcas/{id}',        [AdminBrandController::class, 'update'])->name('marcas.update');
    Route::delete('marcas/{id}',       [AdminBrandController::class, 'destroy'])->name('marcas.destroy');

    // Etiquetas
    Route::post('etiquetas',           [AdminTagController::class, 'store'])->name('etiquetas.store');
    Route::patch('etiquetas/{id}',     [AdminTagController::class, 'update'])->name('etiquetas.update');
    Route::delete('etiquetas/{id}',    [AdminTagController::class, 'destroy'])->name('etiquetas.destroy');

    // Marketing
    Route::get('marketing/campanas',              [AdminCampaignController::class,    'index'])->name('campanas.index');
    Route::post('marketing/campanas',             [AdminCampaignController::class,    'store'])->name('campanas.store');
    Route::patch('marketing/campanas/{id}',       [AdminCampaignController::class,    'update'])->name('campanas.update');
    Route::delete('marketing/campanas/{id}',      [AdminCampaignController::class,    'destroy'])->name('campanas.destroy');

    Route::get('marketing/banners',               [AdminPromoBannerController::class, 'index'])->name('banners.index');
    Route::post('marketing/banners',              [AdminPromoBannerController::class, 'store'])->name('banners.store');
    Route::patch('marketing/banners/{id}',        [AdminPromoBannerController::class, 'update'])->name('banners.update');
    Route::delete('marketing/banners/{id}',       [AdminPromoBannerController::class, 'destroy'])->name('banners.destroy');

    Route::post('marketing/hero-slides',          [AdminPromoBannerController::class, 'heroStore'])->name('hero-slides.store');
    Route::patch('marketing/hero-slides/{id}',    [AdminPromoBannerController::class, 'heroUpdate'])->name('hero-slides.update');
    Route::delete('marketing/hero-slides/{id}',   [AdminPromoBannerController::class, 'heroDestroy'])->name('hero-slides.destroy');

    Route::get('marketing/newsletter',            [AdminNewsletterController::class,  'index'])->name('newsletter.index');
    Route::get('marketing/newsletter/export',     [AdminNewsletterController::class,  'export'])->name('newsletter.export');
    Route::delete('marketing/newsletter/{id}',    [AdminNewsletterController::class,  'destroy'])->name('newsletter.destroy');

    Route::get('marketing/gift-cards',            [AdminGiftCardController::class,    'index'])->name('gift-cards.index');
    Route::post('marketing/gift-cards',           [AdminGiftCardController::class,    'store'])->name('gift-cards.store');
    Route::patch('marketing/gift-cards/{id}',     [AdminGiftCardController::class,    'update'])->name('gift-cards.update');
    Route::delete('marketing/gift-cards/{id}',    [AdminGiftCardController::class,    'destroy'])->name('gift-cards.destroy');

    // Pedidos
    Route::get('pedidos', [AdminOrderController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{id}', [AdminOrderController::class, 'show'])->name('pedidos.show');
    Route::patch('pedidos/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('pedidos.status');

    // Multimedia (solo admin)
    Route::middleware('role:admin')->prefix('multimedia')->name('media.')->group(function () {
        Route::get('/',           [AdminMediaController::class, 'index'])->name('index');
        Route::post('/upload',    [AdminMediaController::class, 'upload'])->name('upload');
        Route::get('/picker',     [AdminMediaController::class, 'picker'])->name('picker');
        Route::get('/{media}',    [AdminMediaController::class, 'show'])->name('show');
        Route::patch('/{media}/alt', [AdminMediaController::class, 'updateAlt'])->name('alt');
        Route::delete('/{media}', [AdminMediaController::class, 'destroy'])->name('destroy');
    });

    // Contenido (solo admin)
    Route::middleware('role:admin')->prefix('contenido')->name('content.')->group(function () {
        Route::get('info-tienda',    [AdminContentController::class, 'storeInfo'])->name('store-info');
        Route::post('info-tienda',   [AdminContentController::class, 'updateStoreInfo'])->name('store-info.update');

        Route::get('quienes-somos',  [AdminContentController::class, 'aboutUs'])->name('about-us');
        Route::post('quienes-somos', [AdminContentController::class, 'updateAboutUs'])->name('about-us.update');

        Route::get('faq',            [AdminContentController::class, 'faq'])->name('faq');
        Route::post('faq',           [AdminContentController::class, 'updateFaq'])->name('faq.update');

        Route::get('terminos',       [AdminContentController::class, 'terms'])->name('terms');
        Route::post('terminos',      [AdminContentController::class, 'updateTerms'])->name('terms.update');

        Route::get('privacidad',     [AdminContentController::class, 'privacy'])->name('privacy');
        Route::post('privacidad',    [AdminContentController::class, 'updatePrivacy'])->name('privacy.update');
    });

    // Configuración (solo admin)
    Route::middleware('role:admin')->prefix('configuracion')->name('settings.')->group(function () {
        Route::get('envios', [AdminSettingsController::class, 'shipping'])->name('shipping');
        Route::match(['POST', 'PUT'], 'envios', [AdminSettingsController::class, 'updateShipping'])->name('shipping.update');

        Route::get('pagos', [AdminSettingsController::class, 'payments'])->name('payments');
        Route::post('pagos/{provider}', [AdminSettingsController::class, 'updatePayment'])->name('payments.update');
        Route::post('pagos/{provider}/validate', [AdminSettingsController::class, 'validatePayment'])->name('payments.validate');

        Route::get('seo', [AdminSettingsController::class, 'seo'])->name('seo');
        Route::match(['POST', 'PUT'], 'seo', [AdminSettingsController::class, 'updateSeo'])->name('seo.update');

        Route::get('analytics', [AdminSettingsController::class, 'analytics'])->name('analytics');
        Route::match(['POST', 'PUT'], 'analytics', [AdminSettingsController::class, 'updateAnalytics'])->name('analytics.update');

        Route::get('email', [AdminSettingsController::class, 'email'])->name('email');
        Route::post('email', [AdminSettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('email/test', [AdminSettingsController::class, 'sendTestEmail'])->name('email.test');

        Route::get('hcaptcha', [AdminSettingsController::class, 'hcaptcha'])->name('hcaptcha');
        Route::match(['POST', 'PUT'], 'hcaptcha', [AdminSettingsController::class, 'updateHcaptcha'])->name('hcaptcha.update');

        Route::get('auditoria', [AdminAuditLogController::class, 'index'])->name('audit.index');

        Route::get('mantenimiento',  [AdminSettingsController::class, 'maintenance'])->name('maintenance');
        Route::post('mantenimiento', [AdminSettingsController::class, 'updateMaintenance'])->name('maintenance.update');
        Route::get('mantenimiento/preview', fn() => view('maintenance', [
            'message'        => \App\Models\SiteContent::getByKey('maintenance')?->metadata['message'] ?? 'Estamos en mantenimiento.',
            'estimated_time' => \App\Models\SiteContent::getByKey('maintenance')?->metadata['estimated_time'] ?? '',
        ]))->name('maintenance.preview');
    });
});
