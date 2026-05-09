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
Route::get('/volunteer/profile', [VolunteerController::class, 'profile'])->name('volunteer.profile.edit')->middleware('auth');
Route::post('/volunteer/register', [VolunteerController::class, 'register'])->name('volunteer.register')->middleware('auth');
Route::get('/volunteer/dashboard', [VolunteerController::class, 'dashboard'])->name('volunteer.dashboard')->middleware('auth');
Route::get('/volunteer/schedules/{schedule}', [VolunteerController::class, 'showSchedule'])->name('volunteer.schedule.show')->middleware('auth');
Route::post('/volunteer/hours', [VolunteerController::class, 'logHours'])->name('volunteer.hours')->middleware('auth');
Route::post('/volunteer/slot-requests', [\App\Http\Controllers\VolunteerSlotController::class, 'store'])->name('volunteer.slot-requests.store')->middleware('auth');
Route::patch('/volunteer/slot-requests/{id}/complete', [\App\Http\Controllers\VolunteerSlotController::class, 'markComplete'])->name('volunteer.slot-requests.complete')->middleware('auth');

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
Route::get('/impact/{report:slug}', [ImpactReportController::class, 'show'])->name('impact.show');

// ─── Stripe Webhook (excluded from CSRF in bootstrap/app.php) ────────────────
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');

// ─── Admin Routes (blade-based, supplemental to Filament panel) ──────────────
Route::prefix('admin')->name('custom_admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/ledger', [AdminController::class, 'ledger'])->name('ledger');
    Route::get('/donors', [AdminController::class, 'donors'])->name('donors');
    Route::post('/manage-schedules/{schedule}/assign', [AdminController::class, 'assignVolunteer'])->name('volunteer-schedules.assign');
    Route::delete('/manage-schedules/{schedule}/unassign/{volunteer}', [AdminController::class, 'unassignVolunteer'])->name('volunteer-schedules.unassign');

    // --- Hybrid Management Routes ---
    Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class)->names([
        'index' => 'campaigns.index',
        'create' => 'campaigns.create',
        'store' => 'campaigns.store',
        'edit' => 'campaigns.edit',
        'update' => 'campaigns.update',
        'destroy' => 'campaigns.destroy',
    ]);

    // Donations Ledger (Renamed URL to avoid Filament conflict)
    Route::get('/manage-donations', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
    Route::get('/manage-donations/{id}', [\App\Http\Controllers\Admin\DonationController::class, 'show'])->name('donations.show');
    
    // Donors (Renamed URL to avoid Filament conflict)
    Route::get('/manage-donors', [\App\Http\Controllers\Admin\DonorController::class, 'index'])->name('donors.index');
    Route::get('/manage-donors/{id}', [\App\Http\Controllers\Admin\DonorController::class, 'show'])->name('donors.show');
    
    // Volunteers Management (Renamed URL to avoid Filament conflict)
    Route::get('/manage-volunteers', [\App\Http\Controllers\Admin\VolunteerController::class, 'index'])->name('volunteers.index');
    Route::get('/manage-volunteers/{id}', [\App\Http\Controllers\Admin\VolunteerController::class, 'show'])->name('volunteers.show');
    Route::patch('/manage-volunteers/{id}/status', [\App\Http\Controllers\Admin\VolunteerController::class, 'updateStatus'])->name('volunteers.update-status');

    // Volunteer Hour Approval
    Route::get('/volunteer-hours', [\App\Http\Controllers\Admin\VolunteerHourController::class, 'index'])->name('volunteer-hours.index');
    Route::post('/volunteer-hours/{log}/approve', [\App\Http\Controllers\Admin\VolunteerHourController::class, 'approve'])->name('volunteer-hours.approve');

    // Volunteer Slot Requests
    Route::get('/volunteer-slots', [\App\Http\Controllers\Admin\VolunteerSlotController::class, 'index'])->name('volunteer-slots.index');
    Route::patch('/volunteer-slots/{id}/approve', [\App\Http\Controllers\Admin\VolunteerSlotController::class, 'approve'])->name('volunteer-slots.approve');
    Route::patch('/volunteer-slots/{id}/reject', [\App\Http\Controllers\Admin\VolunteerSlotController::class, 'reject'])->name('volunteer-slots.reject');

    // Impact Reports Resource (Changed URL to avoid Filament conflict)
    Route::resource('manage-impact-reports', \App\Http\Controllers\Admin\ImpactReportController::class)->names([
        'index' => 'impact-reports.index',
        'create' => 'impact-reports.create',
        'store' => 'impact-reports.store',
        'edit' => 'impact-reports.edit',
        'update' => 'impact-reports.update',
        'destroy' => 'impact-reports.destroy',
    ]);

    // Full Schedule Management (Renamed URL to avoid Filament conflict)
    Route::resource('manage-schedules', \App\Http\Controllers\Admin\VolunteerScheduleController::class)->names([
        'index' => 'schedules.index',
        'create' => 'schedules.create',
        'store' => 'schedules.store',
        'edit' => 'schedules.edit',
        'update' => 'schedules.update',
        'destroy' => 'schedules.destroy',
    ]);
});
