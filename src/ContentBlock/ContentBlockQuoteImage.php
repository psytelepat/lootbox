<?php

namespace Psytelepat\Lootbox\ContentBlock;

use Psytelepat\Lootbox\ContentBlock\BaseImage;
use Illuminate\Database\Eloquent\Model;

class ContentBlockQuoteImage extends BaseImage
{
    protected $table = 'content_block_quote_image';
    protected static $storage_path = 'content-block/images/quote';
}
