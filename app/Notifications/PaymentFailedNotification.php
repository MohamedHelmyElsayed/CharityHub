<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public array $errorPayload
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Urgent: Your recurring donation payment failed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We were unable to process your recurring donation for ' . ($this->subscription->campaign->title ?? 'CharityHub') . '.')
            ->line('Reason: ' . ($this->errorPayload['message'] ?? 'Unknown payment issue'))
            ->line('Please update your payment method to ensure your support continues uninterrupted.')
            ->action('Update Payment Method', route('donor.dashboard'))
            ->line('If you have any questions, please contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'subscription_id' => $this->subscription->id,
            'reason' => $this->errorPayload['message'] ?? 'Unknown error',
            'message' => 'Payment failed for your subscription to ' . ($this->subscription->campaign->title ?? 'CharityHub') . '.',
        ];
    }
}
