<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail(Request $request): void
    {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('recipient@example.com')
                    ->subject('Test Email');
        });
    }
}
