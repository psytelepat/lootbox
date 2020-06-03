<?php

namespace Psytelepat\Lootbox\Publicator;

use Psytelepat\Lootbox\ContentBlock\BaseImage;

class PublicatorAuthorAvatar extends BaseImage
{
    protected $table = 'publicator_author_avatar';
    protected static $storage_path = 'publicator/author/avatar';
}
