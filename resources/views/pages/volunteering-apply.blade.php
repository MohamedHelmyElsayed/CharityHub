@extends('layouts.app')
@section('title', 'Apply — ' . $event->title)

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
*{font-family:'Inter',sans-serif}
.form-input{width:100%;padding:.875rem 1.25rem;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:1rem;font-size:.9rem;font-weight:500;color:#1e293b;transition:border-color .18s,box-shadow .18s}
.form-input:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.12);background:#fff}
.form-label{display:block;font-size:.8rem;font-weight:700;color:#475569;margin-bottom:.4rem;letter-spacing:.02em;text-transform:uppercase}
.section-title{font-size:1rem;font-weight:800;color:#1e293b;padding-bottom:.75rem;border-bottom:2px solid #f1f5f9;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem}
.section-title::before{content:'';display:inline-block;width:4px;height:1rem;border-radius:999px;background:linear-gradient(135deg,#7c3aed,#2563eb)}
</style>

<div class="min-h-screen" style="background:linear-gradient(135deg,#f8fafc 0%,#ede9fe22 100%)">

    {{-- ── TOP BAR ──────────────────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#1e1b4b,#312e81)" class="px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('volunteering.show', $event->slug) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/70 hover:text-white mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Opportunity
            </a>
            <h1 class="text-2xl font-black text-white">Apply for:</h1>
            <p class="text-violet-200 font-bold text-xl mt-1">{{ $event->title }}</p>
            <p class="text-white/60 text-sm mt-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $event->location }}
            </p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl border" style="background:#fee2e2;border-color:#fca5a5">
            <p class="font-bold text-red-800 text-sm mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-red-700 text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Application Form --}}
        <form method="POST" action="{{ route('volunteering.apply.store', $event->slug) }}"
              class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 space-y-8">
            @csrf

            {{-- Why do you want to volunteer? --}}
            <div>
                <h2 class="section-title">Your Motivation</h2>
                <label class="form-label" for="motivation">Why do you want to volunteer for this opportunity?*</label>
                <textarea id="motivation" name="motivation" rows="5" required
                          class="form-input resize-none"
                          placeholder="Tell us what inspires you to join this opportunity and how you hope to contribute…">{{ old('motivation') }}</textarea>
                <p class="text-xs text-slate-400 mt-1.5">Minimum 30 characters.</p>
            </div>

            {{-- Skills --}}
            <div>
                <h2 class="section-title">Skills You Can Contribute</h2>
                <label class="form-label" for="skills_offered">What skills, knowledge, or talents will you bring?*</label>
                <textarea id="skills_offered" name="skills_offered" rows="3" required
                          class="form-input resize-none"
                          placeholder="e.g. First aid, graphic design, Arabic/English translation, cooking, driving license…">{{ old('skills_offered') }}</textarea>
                @if(!empty($event->required_skills))
                <p class="text-xs text-slate-400 mt-1.5">
                    This opportunity looks for: {{ implode(', ', $event->required_skills) }}
                </p>
                @endif
            </div>

            {{-- Experience --}}
            <div>
                <h2 class="section-title">Previous Experience</h2>
                <label class="form-label" for="experience">Any previous volunteer experience? <span class="text-slate-400 normal-case font-normal">(optional)</span></label>
                <textarea id="experience" name="experience" rows="3"
                          class="form-input resize-none"
                          placeholder="Describe any relevant volunteer work, events, or organizations you've been part of…">{{ old('experience') }}</textarea>
            </div>

            {{-- Availability --}}
            <div>
                <h2 class="section-title">Your Availability</h2>
                <label class="form-label" for="availability">When are you available?*</label>
                <input id="availability" type="text" name="availability" required
                       class="form-input"
                       value="{{ old('availability') }}"
                       placeholder="e.g. Weekends, Weekday mornings, Full-time for the event dates…">
                <p class="text-xs text-slate-400 mt-1.5">
                    Event runs {{ $event->start_date->format('M d') }} – {{ $event->end_date->format('M d, Y') }}
                </p>
            </div>

            {{-- Notes --}}
            <div>
                <h2 class="section-title">Additional Notes</h2>
                <label class="form-label" for="notes">Anything else you'd like us to know? <span class="text-slate-400 normal-case font-normal">(optional)</span></label>
                <textarea id="notes" name="notes" rows="2"
                          class="form-input resize-none"
                          placeholder="Special requirements, questions, or additional context…">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="flex-1 py-4 rounded-2xl font-extrabold text-base text-white shadow-lg transition hover:opacity-90 hover:shadow-xl flex items-center justify-center gap-2"
                        style="background:linear-gradient(135deg,#7c3aed,#2563eb)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Submit Application
                </button>
                <a href="{{ route('volunteering.show', $event->slug) }}"
                   class="px-6 py-4 rounded-2xl font-bold text-sm text-slate-600 border border-slate-200 hover:bg-slate-50 transition text-center">
                    Cancel
                </a>
            </div>
            <p class="text-xs text-center text-slate-400">Applications are reviewed within 2–5 business days. You'll be notified by email.</p>
        </form>
    </div>
</div>
@endsection
