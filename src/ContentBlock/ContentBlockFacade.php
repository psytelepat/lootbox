<?php
namespace Psytelepat\Lootbox\ContentBlock;

use Illuminate\Support\Facades\Facade;

class ContentBlockFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ContentBlock';
    }
}
