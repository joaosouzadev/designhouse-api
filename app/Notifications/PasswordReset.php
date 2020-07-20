<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class PasswordReset extends Notification
{
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));

        return (new MailMessage)
                    ->line('Você está recebendo este e-mail pois recebemos uma requisição para mudança de senha.')
                    ->action('Mudar Senha', $url)
                    ->line('Se você não requisitou esta mudança de senha, nenhuma ação é necessária.');
    }
}
