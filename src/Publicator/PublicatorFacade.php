<?php

namespace Psytelepat\Lootbox\Publicator;

use Illuminate\Support\Facades\Facade;

class PublicatorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Publicator';
    }
}
