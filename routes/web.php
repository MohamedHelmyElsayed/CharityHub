<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CampaignController as AdminCampaign;
use App\Http\Controllers\Admin\DonationController as AdminDonation;
use App\Http\Controllers\Admin\VolunteerController as AdminVolunteer;
use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', function () {
    $campaigns = \App\Models\Campaign::where('status', 'active')->take(3)->get();
    return view('pages.home', compact('campaigns'));
})->name('home');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{id}', [CampaignController::class, 'show'])->name('campaigns.show');

Route::get('/donate', [DonationController::class, 'showDonatePage'])->name('donate');
Route::post('/donate/checkout', [DonationController::class, 'createCheckoutSession'])->name('donate.checkout')->middleware('auth');
Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');

Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.index');
Route::post('/volunteer/register', [VolunteerController::class, 'register'])->name('volunteer.register')->middleware('auth');
Route::post('/volunteer/hours', [VolunteerController::class, 'logHours'])->name('volunteer.hours')->middleware('auth');

// Auth Routes (Manual)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Stripe Webhook
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('campaigns', AdminCampaign::class);
    Route::get('/donations', [AdminDonation::class, 'index'])->name('donations.index');
    Route::get('/donations/{id}', [AdminDonation::class, 'show'])->name('donations.show');
    Route::get('/volunteers', [AdminVolunteer::class, 'index'])->name('volunteers.index');
    Route::get('/volunteers/{id}', [AdminVolunteer::class, 'show'])->name('volunteers.show');
    Route::patch('/volunteers/{id}/status', [AdminVolunteer::class, 'updateStatus'])->name('volunteers.update-status');
});
