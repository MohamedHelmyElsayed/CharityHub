<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use App\Models\VolunteerEvent;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Show the application form for a specific opportunity.
     */
    public function create(VolunteerEvent $event)
    {
        // Redirect to login if not authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('info', 'Please log in to apply for this opportunity.');
        }

        // Check if registration is still open
        if ($event->registration_deadline && now()->gt($event->registration_deadline)) {
            return redirect()->route('volunteering.show', $event->slug)
                ->with('error', 'The registration deadline for this opportunity has passed.');
        }

        // Check for existing application
        $existing = VolunteerApplication::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('volunteering.show', $event->slug)
                ->with('info', "You have already applied for this opportunity. Status: {$existing->status}.");
        }

        return view('pages.volunteering-apply', compact('event'));
    }

    /**
     * Store a new opportunity application.
     */
    public function store(Request $request, VolunteerEvent $event)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Double-check deadline
        if ($event->registration_deadline && now()->gt($event->registration_deadline)) {
            return back()->with('error', 'The registration deadline has passed.');
        }

        // Prevent duplicate
        $exists = VolunteerApplication::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return redirect()->route('volunteering.show', $event->slug)
                ->with('info', 'You have already submitted an application for this opportunity.');
        }

        $validated = $request->validate([
            'motivation'     => 'required|string|min:30|max:2000',
            'skills_offered' => 'required|string|min:10|max:1000',
            'experience'     => 'nullable|string|max:1000',
            'availability'   => 'required|string|max:255',
            'notes'          => 'nullable|string|max:500',
        ]);

        VolunteerApplication::create([
            'event_id'       => $event->id,
            'user_id'        => auth()->id(),
            'motivation'     => $validated['motivation'],
            'skills_offered' => $validated['skills_offered'],
            'experience'     => $validated['experience'] ?? null,
            'availability'   => $validated['availability'],
            'notes'          => $validated['notes'] ?? null,
            'status'         => 'pending',
        ]);

        return redirect()->route('volunteering.show', $event->slug)
            ->with('success', 'Your application has been submitted! We will review it and notify you.');
    }
}
