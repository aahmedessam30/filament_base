<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static send()
 * @method static withTitle(string $title)
 * @method static withBody(string $body)
 * @method static withSound(string $sound)
 * @method static withIcon(string $icon)
 * @method static withColor(string $color)
 * @method static withImage(string $image)
 * @method static withAdditionalData(array $additionalData)
 * @method static withClickAction(string $clickAction)
 * @method static withPriority(string $priority)
 */
class Firebase extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase';
    }
}
