<?php

namespace App\Notifications;

use App\Models\Volunteer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VolunteerRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Volunteer $volunteer, public ?string $reason = null) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on Your Volunteer Application — CharityHub')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for your interest in volunteering with CharityHub.')
            ->line('After reviewing your application, we are unable to approve it at this time.');
        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }
        return $mail->line('You are welcome to reapply in the future.')
            ->action('Visit CharityHub', route('home'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'volunteer_rejected',
            'message' => 'Your volunteer application was not approved at this time.',
            'url'     => route('volunteer.index'),
        ];
    }
}
