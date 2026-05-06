<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ImpactReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\StaffDashboardController;

// ─── Auth Routes ───────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');

// ─── Public Campaign Routes ─────────────────────────────────────────────────
Route::get('/', function () {
    $campaigns = \App\Models\Campaign::active()->orderByDesc('featured')->take(6)->get();
    $stats = app(\App\Services\CampaignService::class)->getDashboardStats();
    return view('pages.home', compact('campaigns', 'stats'));
})->name('home');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

// ─── Donation Routes ─────────────────────────────────────────────────────────
Route::get('/donate', [DonationController::class, 'showDonatePage'])->name('donate');
Route::post('/donate/checkout', [DonationController::class, 'createCheckoutSession'])->name('donate.checkout')->middleware('auth');
Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');

// ─── Certificate Verification ────────────────────────────────────────────────
Route::get('/verify/{uuid}', [CertificateController::class, 'verify'])->name('verify.certificate');
Route::get('/certificates/{uuid}/download', [CertificateController::class, 'download'])
    ->name('certificates.download');

// ─── Volunteer Routes ─────────────────────────────────────────────────────────
Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.index');
Route::post('/volunteer/register', [VolunteerController::class, 'register'])->name('volunteer.register')->middleware('auth');
Route::get('/volunteer/dashboard', [VolunteerController::class, 'dashboard'])->name('volunteer.dashboard')->middleware('auth');
Route::post('/volunteer/hours', [VolunteerController::class, 'logHours'])->name('volunteer.hours')->middleware('auth');

// ─── Dashboard Redirection ──────────────────────────────────────────────────
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin() || auth()->user()->isEmployee()) {
        return redirect()->route('custom_admin.dashboard');
    }
    
    // Check if user has a volunteer profile, otherwise go to user dashboard
    if (auth()->user()->volunteer) {
        return redirect()->route('volunteer.dashboard');
    }

    return redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/my-dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard')->middleware('auth');

// ─── Impact Reports ───────────────────────────────────────────────────────────
Route::get('/impact', [ImpactReportController::class, 'index'])->name('impact.index');
Route::get('/impact/{report}', [ImpactReportController::class, 'show'])->name('impact.show');

// ─── Stripe Webhook (excluded from CSRF in bootstrap/app.php) ────────────────
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');

// ─── Admin Routes (blade-based, supplemental to Filament panel) ──────────────
Route::prefix('admin')->name('custom_admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/ledger', [AdminController::class, 'ledger'])->name('ledger');
    Route::get('/donors', [AdminController::class, 'donors'])->name('donors');
    Route::get('/volunteer-schedules', [AdminController::class, 'volunteerSchedules'])->name('volunteer-schedules');
    Route::post('/volunteer-schedules/{schedule}/assign', [AdminController::class, 'assignVolunteer'])->name('volunteer-schedules.assign');

    // --- Hybrid Management Routes ---
    Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class)->names([
        'index' => 'campaigns.index',
        'create' => 'campaigns.create',
        'store' => 'campaigns.store',
        'edit' => 'campaigns.edit',
        'update' => 'campaigns.update',
        'destroy' => 'campaigns.destroy',
    ]);

    Route::get('/donations', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{id}', [\App\Http\Controllers\Admin\DonationController::class, 'show'])->name('donations.show');
    
    Route::get('/volunteers', [\App\Http\Controllers\Admin\VolunteerController::class, 'index'])->name('volunteers.index');
    Route::get('/volunteers/{id}', [\App\Http\Controllers\Admin\VolunteerController::class, 'show'])->name('volunteers.show');
    Route::patch('/volunteers/{id}/status', [\App\Http\Controllers\Admin\VolunteerController::class, 'updateStatus'])->name('volunteers.update-status');
});
