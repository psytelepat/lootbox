<?php

namespace Psytelepat\Lootbox\Publicator;

use Psytelepat\Lootbox\ContentBlock\BaseImage;
use Illuminate\Database\Eloquent\Model;

class SEOImage extends BaseImage
{
    protected $table = 'seo_image';
    protected static $storage_path = 'publicator/images/seo';
}
