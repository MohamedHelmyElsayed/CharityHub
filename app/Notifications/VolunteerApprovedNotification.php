<?php

namespace App\Notifications;

use App\Models\Volunteer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VolunteerApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Volunteer $volunteer) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your Volunteer Application Has Been Approved!')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Great news! Your volunteer application at **CharityHub** has been reviewed and **approved**.')
            ->line('You can now browse available shifts and request assignments.')
            ->action('Go to Your Dashboard', route('volunteer.dashboard'))
            ->line('Thank you for choosing to make a difference!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'volunteer_approved',
            'message' => 'Your volunteer application has been approved! You can now request shifts.',
            'url'     => route('volunteer.dashboard'),
        ];
    }
}
