<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::with(['user', 'campaign'])->latest()->paginate(20);
        return view('admin.donations', compact('donations'));
    }

    public function show($id)
    {
        $donation = Donation::with(['user', 'campaign'])->findOrFail($id);
        return view('admin.donations', compact('donation'));
    }
}
