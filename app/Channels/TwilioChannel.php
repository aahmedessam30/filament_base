<?php

namespace App\Channels;

use Twilio\Rest\Client;
use App\Messages\TwilioWhatsAppMessage;
use Illuminate\Notifications\Notification;
use Twilio\Exceptions\{ConfigurationException, TwilioException};

class TwilioChannel
{
    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function send($notifiable, Notification $notification)
    {
        $message   = $notification->toTwilio($notifiable);
        $twilio    = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        return $twilio->messages->create($this->getTo($notifiable, $message), $this->getParams($message));
    }

    private function getTo($notifiable, $message): string
    {
        return match (get_class($message)) {
            TwilioWhatsAppMessage::class => 'whatsapp:' . $notifiable->routeNotificationFor('Twilio'),
            default                      => $notifiable->routeNotificationFor('Twilio'),
        };
    }

    private function getFrom($message): string
    {
        return match (get_class($message)) {
            TwilioWhatsAppMessage::class => 'whatsapp:' . config('services.twilio.whatsapp_from'),
            default                      => config('services.twilio.from'),
        };
    }

    private function getParams($message)
    {
        $params = [
            'from' => $this->getFrom($message),
            'body' => $message->content,
        ];

        if ($message instanceof TwilioWhatsAppMessage) {
            $params['template_name'] = config('services.twilio.whatsapp_template');
        }

        return $params;
    }
}
