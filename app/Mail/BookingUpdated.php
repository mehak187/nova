<?php

namespace App\Mail;

use App\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $shift;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Réservation modifiée - Calliopée')
            ->markdown('emails.admin.booking-updated', [
                'shift' => $this->shift,
            ]);
    }
}
