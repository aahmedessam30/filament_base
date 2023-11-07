<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FirebaseNotification
{
    const PRIORITY = 'normal';
    const URL = 'https://fcm.googleapis.com/fcm/send';
    private $title;
    private $body;
    private $sound;
    private $icon;
    private $color;
    private $image;
    private $additionalData;
    private $clickAction;
    private $priority = Self::PRIORITY;

    private $model;

    private $tokens = [];

    /**
     * With title
     *
     * @param string $title
     * @return $this
     */
    public function withTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * With body
     *
     * @param string $body
     * @return $this
     */
    public function withBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * With sound
     *
     * @param string $sound
     * @return $this
     */
    public function withSound(string $sound)
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * With icon
     *
     * @param mixed $icon
     * @return $this
     */
    public function withIcon(mixed $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * With click action
     *
     * @param string $clickAction
     * @return $this
     */
    public function withClickAction(string $clickAction)
    {
        $this->clickAction = $clickAction;
        return $this;
    }

    /**
     * With color
     *
     * @param string $color
     * @return $this
     */
    public function withColor(string $color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * With image
     *
     * @param mixed $image
     * @return $this
     */
    public function withImage(mixed $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * With priority
     *
     * @param string $priority
     * @return $this
     */
    public function withPriority(string $priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * With Additional Data
     *
     * @param mixed $additionalData
     * @return $this
     */
    public function withAdditionalData(mixed $additionalData)
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    /**
     * With Model
     *
     * @param Model $model
     * @return $this
     */
    public function withModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * With Token
     *
     * @param string $token
     * @return $this
     */
    public function withToken(string $token)
    {
        $this->tokens = [$token];
        return $this;
    }

    /**
     * With Tokens
     *
     * @param array $tokens
     * @return $this
     */
    public function withTokens(array $tokens = [])
    {
//        if (empty($tokens)) {
//            $tokens = (new $this->model)->all()->pluck('device_key')->toArray();
//        }

        $this->tokens = array_values(array_filter(array_unique($tokens)));
        return $this;
    }

    /**
     * Set Fields for Notification
     *
     * @param array $tokens
     * @return array $fields
     */
    private function setFields(array $tokens)
    {
        $notifications = ['title' => $this->title, 'body'  => $this->body];

        if ($this->sound) {
            $notifications['sound'] = $this->sound;
        }

        if ($this->clickAction) {
            $notifications['click_action'] = $this->clickAction;
        }

        if ($this->icon) {
            $notifications['icon'] = $this->icon;
        }

        if ($this->color) {
            $notifications['color'] = $this->color;
        }

        if ($this->image) {
            $notifications['image'] = $this->image;
        }

        $fields = [
            'registration_ids' => $tokens,
            'notification'     => $notifications,
            'priority'         => $this->priority,
        ];

        if ($this->additionalData) {
            $fields['data'] = $this->additionalData;
        }

        return $fields;
    }

    /**
     * Send Notification
     *
     * @return PromiseInterface|Response
     */
    public function send()
    {
       // dd($this->setFields($this->tokens));
       // dd($this->setFields($this->tokens));

        return Http::withHeaders(['Authorization' => 'key=' . config('services.firebase.server_key')])
            ->post(self::URL, $this->setFields($this->tokens))
            ->json();
    }

}
