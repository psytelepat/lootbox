<?php

namespace Psytelepat\Lootbox\ContentBlock;

use Psytelepat\Lootbox\ContentBlock\BaseImage;
use Illuminate\Database\Eloquent\Model;

class ContentBlockVideoImage extends BaseImage
{
    protected $table = 'content_block_video_image';
    protected static $storage_path = 'content-block/images/video';
}
