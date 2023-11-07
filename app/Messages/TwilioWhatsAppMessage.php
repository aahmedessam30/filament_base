<?php

namespace App\Messages;

class TwilioWhatsAppMessage
{
    public $content;

    public function content($content)
    {
        $this->content = $content;

        return $this;
    }
}
