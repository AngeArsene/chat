<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Facades;

use Illuminate\Support\Facades\Facade;

final class Chat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'chat';
    }
}