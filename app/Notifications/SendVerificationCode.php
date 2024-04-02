<?php

namespace App\Notifications;

use App\Channels\KavenegarChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendVerificationCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public int $code, public User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [KavenegarChannel::class];
    }

    public function toKavenegarSMS($notifiable)
    {
        return [
            'text' => " کد احراز هویت{$this->code} وب سایت دیجی کالا ",
            'mobile' => $this->user->mobile
        ];
    }

}
