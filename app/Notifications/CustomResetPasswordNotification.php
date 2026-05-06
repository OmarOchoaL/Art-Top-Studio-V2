<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de contraseña - Art Top Studio')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Cambiar contraseña', $url)
            ->line('Si tú no solicitaste este cambio, puedes ignorar este correo.')
            ->line('Gracias por confiar en Art Top Studio.');
    }
}
