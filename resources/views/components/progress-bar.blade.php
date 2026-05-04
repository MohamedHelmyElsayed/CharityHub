@props(['goal', 'raised'])

@php
    $percentage = $goal > 0 ? min(100, round(($raised / $goal) * 100)) : 0;
@endphp

<div class="w-full">
    <div class="flex justify-between text-sm mb-2">
        <span class="font-bold text-primary-600">${{ number_format($raised) }} raised</span>
        <span class="text-gray-500 font-medium">${{ number_format($goal) }} goal</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden shadow-inner">
        <div class="bg-primary-500 h-2.5 rounded-full relative" style="width: {{ $percentage }}%">
            <div class="absolute top-0 bottom-0 left-0 right-0 overflow-hidden rounded-full">
                <div class="w-full h-full bg-white opacity-20 -skew-x-12 transform -translate-x-full animate-[shimmer_2s_infinite]"></div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style
