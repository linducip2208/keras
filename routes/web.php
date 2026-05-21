<?php

use Illuminate\Support\Facades\Route;

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

// Public Career Page
Route::get('/karir', [CareerController::class, 'index'])->name('career.index');
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
