<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationCompteAdmission extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token,
        private readonly string $invitePar,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Invitation à l’espace admission EPF')
            ->view('emails.invitation-compte-admission', [
                'nom' => $notifiable->name,
                'invitePar' => $this->invitePar,
                'url' => $url,
                'expiration' => config('auth.passwords.users.expire'),
            ]);
    }
}
