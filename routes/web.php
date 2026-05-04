<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CampaignController as AdminCampaign;
use App\Http\Controllers\Admin\DonationController as AdminDonation;
use App\Http\Controllers\Admin\VolunteerController as AdminVolunteer;

// Public Routes
Route::get('/', function () {
    $campaigns = \App\Models\Campaign::where('status', 'active')->take(3)->get();
    return view('pages.home', compact('campaigns'));
})->name('home');

Route::get('/login', function () {
    return "Login Page Placeholder - Please implement authentication or install Laravel Breeze.";
})->name('login');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{id}', [CampaignController::class, 'show'])->name('campaigns.show');

Route::get('/donate', [DonationController::class, 'showDonatePage'])->name('donate');
Route::post('/donate/checkout', [DonationController::class, 'createCheckoutSession'])->name('donate.checkout')->middleware('auth');
Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');

Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.index');
Route::post('/volunteer/register', [VolunteerController::class, 'register'])->name('volunteer.register')->middleware('auth');
Route::post('/volunteer/hours', [VolunteerController::class, 'logHours'])->name('volunteer.hours')->middleware('auth');

// Stripe Webhook (Exclude from CSRF in VerifyCsrfToken if using older Laravel, but in Laravel 11 it's in bootstrap/app.php)
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Campaign Management
    Route::resource('campaigns', AdminCampaign::class);
    
    // Donation Tracking
    Route::get('/donations', [AdminDonation::class, 'index'])->name('donations.index');
    Route::get('/donations/{id}', [AdminDonation::class, 'show'])->name('donations.show');
    
    // Volunteer Management
    Route::get('/volunteers', [AdminVolunteer::class, 'index'])->name('volunteers.index');
    Route::get('/volunteers/{id}', [AdminVolunteer::class, 'show'])->name('volunteers.show');
    Route::patch('/volunteers/{id}/status', [AdminVolunteer::class, 'updateStatus'])->name('volunteers.update-status');
});


