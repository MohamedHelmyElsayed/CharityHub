<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/campaigns', function () {
    return view('pages.campaigns');
})->name('campaigns.index');

Route::get('/campaigns/1', function () {
    return view('pages.campaign-details');
})->name('campaigns.show');

Route::get('/donate', function () {
    return view('pages.donate');
})->name('donate');

Route::get('/volunteer', function () {
    return view('pages.volunteer');
})->name('volunteer.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/campaigns', function () {
        return view('admin.campaigns');
    })->name('campaigns.index');
    
    Route::get('/donations', function () {
        return view('admin.donations');
    })->name('donations.index');
    
    Route::get('/volunteers', function () {
        return view('admin.volunteers');
    })->name('volunteers.index');
});
