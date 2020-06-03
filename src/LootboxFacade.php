<?php
namespace Psytelepat\Lootbox;

use Illuminate\Support\Facades\Facade;

class LootboxFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Lootbox';
    }
}
