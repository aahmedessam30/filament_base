<?php

namespace App\Notifications;

use App\Channels\TwilioChannel;
use App\Messages\TwilioSmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwilioNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }

    /**
     * Get the Twilio / SMS representation of the notification.
     * classes are located in app/Messages
     * classes are (TwilioSmsMessage, TwilioWhatsAppMessage)
     *
     * @param  mixed  $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content('Your SMS message content');
    }
}
