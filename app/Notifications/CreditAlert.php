<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditAlert extends Notification
{
    use Queueable;

    public $totalPurchasedMinutes;

    public $totalIncludedMinutes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($totalPurchasedMinutes, $totalIncludedMinutes)
    {
        $this->totalPurchasedMinutes = $totalPurchasedMinutes;
        $this->totalIncludedMinutes = $totalIncludedMinutes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre crédit est bientôt épuisé - Calliopée')
            ->markdown('emails.credit-alert', [
                'totalPurchasedMinutes' => $this->totalPurchasedMinutes,
                'totalIncludedMinutes' => $this->totalIncludedMinutes,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
