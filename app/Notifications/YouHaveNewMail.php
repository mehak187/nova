<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouHaveNewMail extends Notification
{
    use Queueable;

    public $content;

    public function __construct(array $content)
    {
        $this->content = $content;
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
            ->subject('Votre courrier est arrivé - Calliopée')
            ->markdown('emails.new-mail', [
                'firstName' => $notifiable->first_name,
                'lastName' => $notifiable->last_name,
                'content' => $this->content,
            ]);
    }
}
