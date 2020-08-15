<?php

namespace Psytelepat\Lootbox\ContentBlock;

use DB;
use File;
use Image;
use Storage;
use ContentBlock;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

class BaseImage extends Model
{
    protected $table;
    protected static $storage_path;
    protected static $sizes;
    public static $preview_size;

    protected $fillable = [
    'usr',
    'lnk',
    'size',
    'type',
    'fn',
    'w',
    'h',
    'fs',
    'alt',
    'title',
    'description',
    'href',
    ];

    protected static function boot()
    {
        parent::boot();

        static::sizes();

        static::deleting(function (BaseImage $image) {
            foreach ($image->childs as $child) {
                $child->delete();
            }
            Storage::disk('public')->delete($image->fullPath());
        });
    }

    public static function sizes() : array
    {
        if (!static::$sizes && ( $sizes = config('lootbox.images.'.static::class))) {
            static::$sizes = $sizes;
            foreach ($sizes as $size) {
                static::$preview_size = $size['size'];
                if (array_key_exists('preview', $size) && $size['preview']) {
                    break;
                }
            }
        }
        return static::$sizes;
    }

    public static function getPreviewSize() : int
    {
        if (!static::$sizes) {
            static::sizes();
        }
        return static::$preview_size;
    }

    protected function generateFileName(UploadedFile $file, string $ext = null) : string
    {
        do {
            $this->fn = str_random(16) . '.' . ( $ext ? $ext : $file->guessExtension() );
        } while (Storage::disk('public')->exists($this->fullPath()));
        return $this->fn;
    }

    public function filePath() : string
    {
        return ( static::$storage_path ? trim(static::$storage_path, '/') . '/' : '' ) . $this->size . '/';
    }

    public function fullPath() : string
    {
        return $this->filePath() . $this->fn;
    }

    public function url() : string
    {
        return Storage::disk('public')->url(( static::$storage_path ? trim(static::$storage_path, '/') . '/' : '' ) . $this->size . '/' . $this->fn);
    }



    public static function createImageForItem(Model $item, int $size, int $lnk) : BaseImage
    {
        $node = new static;

        $node->usr  = $item->id;
        $node->lnk  = $lnk;
        $node->size = $size;

        return $node;
    }

    public static function processFileUpload(Model &$item, UploadedFile $file, string $forceFormat = null) : array
    {

        $pos      = 1;
        $images   = [];
        $rootNode = false;

        foreach (static::sizes() as $size) {
            $forceFormat = Arr::get($size, 'forceFormat', $forceFormat);

            $node = static::createImageForItem($item, $size['size'], $rootNode ? $rootNode->id : 0);

            if (!$rootNode) {
                $rootNode = $node;
                if ($maxpos = static::where('lnk', 0)->max('pos')) {
                    $pos = $maxpos + 1;
                }
            }

            $node->pos = $pos;
            $node->generateFileName($file);
            $node->alt = $file->getClientOriginalName();

            $image = Image::make($file);

            if (static::processFile($image, $size)) {
                if ($forceFormat) {
                    $node->generateFileName($file, $forceFormat);
                }

                Storage::disk('public')->put($node->fullPath(), $image->encode($forceFormat));
                $node->fs = $image->filesize();
            } else {
                $file->storeAs($node->filePath(), $node->fn, 'public');
                $node->fs = $file->getSize();
            }

            $node->h = $image->height();
            $node->w = $image->width();
            $node->save();

            $images[] = $node;
        }

        return $images;
    }

    public static function processFile(\Intervention\Image\Image &$image, array &$size) : bool
    {
        $w = Arr::get($size, 'w', null);
        $h = Arr::get($size, 'h', null);

        if (( ( $w > 0 ) && ( $image->width() > $size['w'] ) ) || ( ( $h > 0 ) && ( $image->height() > $size['h'] ) )) {
            switch (Arr::get($size, 'mode', 'default')) {
                case 'fit':
                    $image->fit($w, $h, function ($r) {
                        $r->upsize();
                    });
                    break;
                default:
                  // $aspect = $image->width() / $image->height();
                  // if(!$w){ $w = $h * $aspect; }
                  // if(!$h){ $h = $w / $aspect; }
                    $image->resize($w, $h, function ($r) {
                        $r->aspectRatio();
                        $r->upsize();
                    });
                    break;
            }

            return true;
        }

        return false;
    }

    public function replaceWithImage(\Intervention\Image\Image &$image, string $forceFormat = null) : bool
    {
        $size = null;
        foreach (static::$sizes as $_size) {
            if (Arr::get($_size, 'size') == $this->size) {
                $size = $_size;
                break;
            }
        }

        if (!$size) {
            return false;
        }

        static::processFile($image, $size);

        $ext = $forceFormat ?: pathinfo($this->fullPath(), PATHINFO_EXTENSION);

        do {
            $this->fn = str_random(16) . '.' . $ext;
        } while (Storage::disk('public')->exists($this->fullPath()));

        Storage::disk('public')->put($this->fullPath(), $image->encode($ext));

        $this->h = $image->height();
        $this->w = $image->width();
        $this->save();

        return true;
    }



    public function parent()
    {
        return $this->belongsTo(static::class, 'lnk');
    }

    protected function childs()
    {
        return $this->hasMany(static::class, 'lnk', 'id');
    }



    public static function repos(int $from_id, int $to_id) : array
    {
    
        $from = static::findOrFail($from_id);
        $to = static::findOrFail($to_id);

        $from->reposTo($to);

        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    public function resetPositions() : void
    {
        $pos = 1;
        foreach (static::where('usr', $this->usr)->orderBy('id')->get() as $image) {
            DB::table($this->table)->where('usr', $image->usr)->where('id', $image->id)->where('lnk', $image->id)->update([ 'pos' => $pos ]);
            $pos++;
        }
    }

    public function reposTo(BaseImage $to) : BaseImage
    {

        if ($this->pos > $to->pos) {
            DB::table($this->table)->where('usr', $this->usr)->where('pos', '>=', $to->pos)->where('pos', '<', $this->pos)->increment('pos', +1);
        } else {
            DB::table($this->table)->where('usr', $this->usr)->where('pos', '<=', $to->pos)->where('pos', '>', $this->pos)->increment('pos', -1);
        }

        DB::table($this->table)->where('id', $this->id)->orWhere('lnk', $this->id)->update([ 'pos' => $to->pos ]);

        return $this;
    }

    public function updateSEO(array $data) : void
    {
        $this->alt = Arr::get($data, 'alt');
        $this->title = Arr::get($data, 'title');
        $this->description = Arr::get($data, 'description');
        $href = Arr::get($data, 'href', null);
        if (!$href || filter_var($href, FILTER_VALIDATE_URL)) {
            $this->href = $href;
        }
        $this->save();

        foreach ($this->childs as $child) {
            $child->alt = $this->alt;
            $child->title = $this->title;
            $child->description = $this->description;
            $child->save();
        }
    }
}
