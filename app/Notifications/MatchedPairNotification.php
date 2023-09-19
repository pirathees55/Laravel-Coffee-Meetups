<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchedPairNotification extends Notification
{
    use Queueable;
    protected $pair;

    /**
     * Create a new notification instance.
     */
     public function __construct($pair)
     {
         $this->pair = $pair;
     }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $partnerName = $this->pair->user2->name;
        $partnerProfileLink = route('user.profile', $this->pair->user2);

        return (new MailMessage)
            ->line('You have been paired with a new coffee roulede partner!')
            ->line('Details of your partner: ' . $partnerName)
            ->action('View Partner Profile', $partnerProfileLink)
            ->line('Thank you for participating in Coffee RouleDe!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
