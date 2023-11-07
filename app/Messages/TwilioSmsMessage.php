<?php

namespace App\Messages;

class TwilioSmsMessage
{
    public $content;

    public function content($content)
    {
        $this->content = $content;

        return $this;
    }
}
