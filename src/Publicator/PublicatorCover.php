<?php

namespace Psytelepat\Lootbox\Publicator;

use Psytelepat\Lootbox\ContentBlock\BaseImage;
use Illuminate\Database\Eloquent\Model;

class PublicatorCover extends BaseImage
{
    protected $table = 'publicator_cover_image';
    protected static $storage_path = 'publicator/images/cover';
}
