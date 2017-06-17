<?php

namespace Polass\Message\Facades;

use Illuminate\Support\Facades\Facade;
use Polass\Message\MessageManager;

class Message extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'polass.message.manager';
    }
}
