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
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Volunteer\ApplicationController;

// ─── Auth Routes ───────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');

// ─── Google OAuth ────────────────────────────────────────────────────────────
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google')->middleware('guest');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

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
Route::post('/donate/checkout', [DonationController::class, 'createCheckoutSession'])->name('donate.checkout')->middleware(['auth', 'idempotency']);
Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');

// ─── Certificate Verification ────────────────────────────────────────────────
Route::get('/verify', [CertificateController::class, 'index'])->name('verify.index');
Route::get('/verify/{uuid}', [CertificateController::class, 'verify'])->name('verify.certificate');
Route::get('/certificates/{uuid}/download', [CertificateController::class, 'download'])
    ->name('certificates.download');

// ─── Static Pages ────────────────────────────────────────────────────────────
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms', fn() => view('pages.terms'))->name('terms');

// ─── Volunteering Opportunities (new public system) ──────────────────────────
Route::get('/volunteering', [VolunteerController::class, 'opportunities'])->name('volunteering.index');
Route::get('/volunteering/{event:slug}', [VolunteerController::class, 'showOpportunity'])->name('volunteering.show');
Route::get('/volunteering/{event:slug}/apply', [ApplicationController::class, 'create'])->name('volunteering.apply')->middleware('auth');
Route::post('/volunteering/{event:slug}/apply', [ApplicationController::class, 'store'])->name('volunteering.apply.store')->middleware('auth');

// ─── Volunteer Routes (legacy — redirect old /volunteer to new /volunteering) ──
Route::get('/volunteer', fn() => redirect()->route('volunteering.index'))->name('volunteer.index');
Route::get('/volunteer/profile', [VolunteerController::class, 'profile'])->name('volunteer.profile.edit')->middleware('auth');
Route::post('/volunteer/register', [VolunteerController::class, 'register'])->name('volunteer.register')->middleware('auth');
Route::get('/volunteer/dashboard', [VolunteerController::class, 'dashboard'])->name('volunteer.dashboard')->middleware('auth');
Route::get('/volunteer/schedules/{schedule}', [VolunteerController::class, 'showSchedule'])->name('volunteer.schedule.show')->middleware('auth');
Route::post('/volunteer/hours', [VolunteerController::class, 'logHours'])->name('volunteer.hours')->middleware('auth');

// Shift Requests (new system)
Route::middleware('auth')->group(function () {
    Route::post('/volunteer/shifts/request', [\App\Http\Controllers\Volunteer\ShiftRequestController::class, 'store'])->name('volunteer.shifts.request');
    Route::patch('/volunteer/shifts/requests/{slotRequest}/cancel', [\App\Http\Controllers\Volunteer\ShiftRequestController::class, 'cancel'])->name('volunteer.shifts.requests.cancel');

    // Self Check-in / Check-out
    Route::post('/volunteer/attendance/check-in', [\App\Http\Controllers\Volunteer\AttendanceController::class, 'selfCheckIn'])->name('volunteer.attendance.check-in');
    Route::post('/volunteer/attendance/{log}/check-out', [\App\Http\Controllers\Volunteer\AttendanceController::class, 'selfCheckOut'])->name('volunteer.attendance.check-out');
});

// Legacy slot requests
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

Route::middleware('auth')->group(function () {
    Route::get('/my-dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/my-profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::put('/my-profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/my-profile/password', [UserDashboardController::class, 'updatePassword'])->name('user.profile.password');
    Route::delete('/my-profile', [UserDashboardController::class, 'deleteAccount'])->name('user.profile.delete');
});

// ─── Donor Dashboard & Subscription Management ──────────────────────────────
Route::middleware('auth')->prefix('donor')->name('donor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Donor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/donations', [\App\Http\Controllers\Donor\DashboardController::class, 'donationHistory'])->name('donations.history');
    Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Donor\DashboardController::class, 'cancelSubscription'])->name('subscriptions.cancel');
});

// ─── Impact Reports ───────────────────────────────────────────────────────────
Route::get('/impact', [ImpactReportController::class, 'index'])->name('impact.index');
Route::get('/impact/{report:slug}', [ImpactReportController::class, 'show'])->name('impact.show');
Route::get('/impact/{report:slug}/pdf', [ImpactReportController::class, 'downloadPdf'])->name('impact.download-pdf');

// ─── Stripe Webhook (excluded from CSRF in bootstrap/app.php) ────────────────
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');

// ─── Admin Routes (blade-based, supplemental to Filament panel) ──────────────
Route::get('/admin/filament-dashboard-alias', function () {
    return redirect()->route('custom_admin.dashboard');
})->name('filament.admin.pages.dashboard')->middleware(['auth', 'role:admin,employee']);

Route::get('/admin/filament-donors-alias', function () {
    return redirect()->route('custom_admin.donors');
})->name('filament.admin.resources.donors.index')->middleware(['auth', 'role:admin,employee']);

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
    Route::post('/manage-donations/{id}/refund', [\App\Http\Controllers\Admin\DonationController::class, 'refund'])->name('donations.refund');

    // Donors (Renamed URL to avoid Filament conflict)
    // Route::get('/manage-donors', [\App\Http\Controllers\Admin\DonorController::class, 'index'])->name('donors.index');
    // Route::get('/manage-donors/{id}', [\App\Http\Controllers\Admin\DonorController::class, 'show'])->name('donors.show');


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

    // Volunteer Shifts
    Route::post('/manage-schedules/{eventId}/shifts', [\App\Http\Controllers\Admin\VolunteerShiftController::class, 'store'])->name('schedules.shifts.store');
    Route::delete('/manage-shifts/{id}', [\App\Http\Controllers\Admin\VolunteerShiftController::class, 'destroy'])->name('schedules.shifts.destroy');

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
