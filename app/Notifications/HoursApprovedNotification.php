<?php

namespace App\Notifications;

use App\Models\HourLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HoursApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public HourLog $hourLog) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $hours = $this->hourLog->approved_hours ?? $this->hourLog->calculated_hours;
        return (new MailMessage)
            ->subject('⏱️ Volunteer Hours Approved — CharityHub')
            ->greeting("Hello {$notifiable->name}!")
            ->line("**{$hours} hours** from your recent volunteer shift have been approved.")
            ->line("Your total approved hours have been updated on your profile.")
            ->action('View Your Profile', route('volunteer.dashboard'))
            ->line('Thank you for your dedication!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'hours_approved',
            'message' => "Your volunteer hours ({$this->hourLog->final_hours} hrs) have been approved!",
            'url'     => route('volunteer.dashboard'),
        ];
    }
}
