<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *@method static sendMessage($id, mixed $body)
 * @method static readMessage($room)
 * @method static createRoom($members)
 */
class Chat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "App\\Services\\Chat\\" . ucfirst(in_array(config('broadcasting.default'), ['pusher', 'websockets'])
                ? config('broadcasting.default')
                : 'pusher');
    }
}
