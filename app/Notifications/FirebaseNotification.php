<?php

namespace App\Notifications;

use App\Facades\Firebase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FirebaseNotification extends Notification
{
    use Queueable;

    private $fields;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
        Firebase::withTitle($this->fields['title'])
            ->withBody($this->fields['body'])
            ->withTokens($notifiable->device_tokens)
            ->send();

        return $this->fields;
    }

}
