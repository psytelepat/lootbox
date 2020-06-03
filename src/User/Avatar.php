<?php

namespace Psytelepat\Lootbox\User;

use Psytelepat\Lootbox\ContentBlock\BaseImage;

class Avatar extends BaseImage
{
    protected $table = 'avatar';
    protected static $storage_path = 'user/avatar';
}
