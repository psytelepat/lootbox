<?php

namespace Psytelepat\Lootbox\ContentBlock;

use Psytelepat\Lootbox\ContentBlock\BaseImage;
use Illuminate\Database\Eloquent\Model;

class ContentBlockImage extends BaseImage
{
    protected $table = 'content_block_image';
    protected static $storage_path = 'content-block/images/default';
}
