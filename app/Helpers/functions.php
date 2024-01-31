<?php

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

function notifyAdmins(Mailable $mailable)
{
    try {
        retry(3, function () use ($mailable) {
            Mail::to(config('calliopee.admin_email'))->send($mailable);
        }, 1000);
    } catch (\Exception $e) {
        // do nothing
    }
}
