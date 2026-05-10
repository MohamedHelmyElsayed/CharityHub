<?php

namespace App\Notifications;

use App\Models\Donation;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public Donation $donation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thank you for your continued support! - CharityHub')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your monthly recurring donation has been successfully renewed.')
            ->line('Amount: ' . number_format($this->donation->amount, 2) . ' ' . $this->donation->currency)
            ->line('Campaign: ' . ($this->subscription->campaign->title ?? 'General Fund'))
            ->action('View Donation Receipt', route('donor.history'))
            ->line('Thank you for helping us make a difference!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_renewed',
            'amount' => $this->donation->amount,
            'currency' => $this->donation->currency,
            'subscription_id' => $this->subscription->id,
            'message' => 'Your subscription for ' . ($this->subscription->campaign->title ?? 'CharityHub') . ' has been renewed.',
        ];
    }
}
