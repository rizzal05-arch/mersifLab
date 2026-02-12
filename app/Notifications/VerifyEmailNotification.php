<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public $user;
    public $verificationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Anda - MersifLab')
            ->greeting('Halo ' . $this->user->name . ',')
            ->line('Terima kasih telah mendaftar di MersifLab.')
            ->line('Silakan klik tombol di bawah untuk memverifikasi email Anda.')
            ->action('Verifikasi Email', $this->verificationUrl)
            ->line('Atau salin link berikut ke browser Anda:')
            ->line($this->verificationUrl)
            ->line('Link verifikasi ini akan berlaku selama 24 jam.')
            ->line('')
            ->line('Jika Anda tidak membuat akun ini, abaikan email ini.')
            ->line('')
            ->line('Regards,')
            ->line('Tim MersifLab');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
