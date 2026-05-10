<?php

namespace App\Notifications;

use App\Models\VolunteerSlotRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public VolunteerSlotRequest $request) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $shift = $this->request->shift;
        $event = $shift?->event;
        return (new MailMessage)
            ->subject('✅ Shift Request Approved — CharityHub')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your shift request has been **approved**.")
            ->line("**Event:** " . ($event?->title ?? 'N/A'))
            ->line("**Shift:** " . ($shift?->title ?? 'N/A'))
            ->line("**Date:** " . ($shift?->shift_date?->format('M d, Y') ?? 'N/A'))
            ->line("**Time:** " . ($shift?->start_time ?? '') . ' – ' . ($shift?->end_time ?? ''))
            ->line("**Location:** " . ($shift?->location ?? $event?->location ?? 'TBD'))
            ->action('View My Dashboard', route('volunteer.dashboard'))
            ->line('We look forward to seeing you there!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'     => 'shift_approved',
            'message'  => 'Your shift request has been approved!',
            'shift_id' => $this->request->shift_id,
            'url'      => route('volunteer.dashboard'),
        ];
    }
}
