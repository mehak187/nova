<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailNotificationController extends Controller
{
    public function index()
    {
        auth('app')->user()->update([
            'has_unread_mail_notifications' => false,
        ]);

        return view('mail-notifications.index', [
            'notifications' => auth('app')->user()->mailNotifications()->latest()->paginate(15)
        ]);
    }
}
