<?php

namespace App\Channels;

use App\Messages\TwilioWhatsAppMessage;
use Illuminate\Notifications\Notification;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioChannel
{
    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function send($notifiable, Notification $notification)
    {
        $message   = $notification->toTwilio($notifiable);
        $fromAndTo = $this->getFromAndTo($notification, $notifiable);
        $to        = $fromAndTo['to'];
        $from      = $fromAndTo['from'];
        $twilio    = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        return $twilio->messages->create($to, ["from" => $from, "body" => $message->content]);
    }

    protected function getFromAndTo(Notification $notification, $notifiable)
    {
        $message = $notification->toTwilio($notifiable);

        if ($message instanceof TwilioWhatsAppMessage) {
            $to   = 'whatsapp:' . $notifiable->routeNotificationFor('Twilio');
            $from = 'whatsapp:' . config('services.twilio.whatsapp_from');
        } else {
            $to   = $notifiable->routeNotificationFor('Twilio');
            $from = config('services.twilio.from');
        }

        return ['to' => $to, 'from' => $from];
    }
}
