<?php

namespace App\Notifications;

use App\Models\VolunteerSlotRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public VolunteerSlotRequest $request) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $shift = $this->request->shift;
        $mail = (new MailMessage)
            ->subject('Shift Request Update — CharityHub')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your request for **" . ($shift?->title ?? 'a shift') . "** could not be approved at this time.");
        if ($this->request->rejection_reason) {
            $mail->line("**Reason:** {$this->request->rejection_reason}");
        }
        return $mail->line('Please check our available shifts for other opportunities.')
            ->action('Browse Shifts', route('volunteer.dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'shift_rejected',
            'message' => 'Your shift request was not approved. Check for other available shifts.',
            'url'     => route('volunteer.dashboard'),
        ];
    }
}
