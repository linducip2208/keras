<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\CareerController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProgrammaticSeoController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }
    return view('marketing.home');
})->name('home');

// pSEO routes (per CLAUDE.md §1)
Route::get('/best-aplikasi-hris-{year?}', [ProgrammaticSeoController::class, 'bestHris'])
    ->where('year', '[0-9]{4}')->name('pseo.best');
Route::get('/best-aplikasi-hris-untuk-{industry}', [ProgrammaticSeoController::class, 'bestHrisForIndustry'])
    ->name('pseo.industry');
Route::get('/aplikasi-hris-{city}', [ProgrammaticSeoController::class, 'hrisForCity'])
    ->name('pseo.city');
Route::get('/compare/hris-vs-{competitor}', [ProgrammaticSeoController::class, 'compare'])
    ->name('pseo.compare');
Route::get('/alternatives-to-{competitor}', [ProgrammaticSeoController::class, 'alternatives'])
    ->name('pseo.alternatives');

// Sitemap & robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Public Billing — Tenant Pay Invoice
Route::get('/billing/pay/{token}', [BillingController::class, 'pay'])->name('billing.pay');
Route::post('/billing/pay/{token}', [BillingController::class, 'process'])->name('billing.process')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
Route::get('/billing/instruction/{token}/{payment}', [BillingController::class, 'instruction'])->name('billing.instruction');
Route::get('/billing/success/{token}', [BillingController::class, 'success'])->name('billing.success');
Route::get('/billing/cancel/{token}', [BillingController::class, 'cancel'])->name('billing.cancel');

// Public Career Page (Hybrid: marketplace + per-tenant)
Route::get('/karir', [CareerController::class, 'index'])->name('career.index'); // marketplace
Route::get('/karir/perusahaan/{slug}', [CareerController::class, 'tenant'])->name('career.tenant')
    ->where('slug', '[a-z0-9-]+'); // per-tenant
Route::get('/karir/{token}', [CareerController::class, 'show'])->name('career.show')
    ->where('token', '[A-Za-z0-9_-]+');
Route::post('/karir/{token}/apply', [CareerController::class, 'apply'])->name('career.apply')
    ->where('token', '[A-Za-z0-9_-]+');

// Payment webhooks (inbound from gateways) — no CSRF
Route::post('/webhook/payment/{adapterConfig}', [PaymentWebhookController::class, 'handle'])
    ->name('webhook.payment')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

// WhatsApp webhooks
Route::get('/webhook/whatsapp/{adapterConfig}', [WhatsAppWebhookController::class, 'verify'])
    ->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp/{adapterConfig}', [WhatsAppWebhookController::class, 'handle'])
    ->name('webhook.whatsapp')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

require base_path('routes/pair-routes.php');
