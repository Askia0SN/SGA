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
            ->subject('Invitation a l espace admission EPF')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->invitePar.' a cree votre acces au systeme d admission EPF.')
            ->line('Utilisez le bouton ci-dessous pour definir votre mot de passe et activer votre acces.')
            ->action('Definir mon mot de passe', $url)
            ->line('Ce lien expire dans '.config('auth.passwords.users.expire').' minutes.')
            ->line('Si vous ne reconnaissez pas cette invitation, ignorez cet email.');
    }
}
