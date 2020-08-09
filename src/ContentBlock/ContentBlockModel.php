<?php

namespace Psytelepat\Lootbox\ContentBlock;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use DB;
use ContentBlock;
use Psytelepat\Lootbox\ContentBlock\ContentBlockImage;
use Psytelepat\Lootbox\ContentBlock\ContentBlockQuoteImage;
use Psytelepat\Lootbox\ContentBlock\ContentBlockVideoImage;

class ContentBlockModel extends Model
{
    protected $table = 'content_block';
    protected $image_class = ContentBlockImage::class;
    protected $quote_image_class = ContentBlockQuoteImage::class;
    protected $video_image_class = ContentBlockVideoImage::class;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (ContentBlockModel $item) {
            $item->clearImages();
            $item->clearQuoteImages();
        });
    }

    public static $alignClass = array(false,'align-left','align-right');

    public function isAligned(): bool
    {
        return ( $this->align || ( ( $this->mode == 4 || $this->mode == 2 ) && ($this->style == 0) && strlen($this->content) ) );
    }

    public function reposTo(ContentBlockModel $to): ContentBlockModel
    {
        if ($this->pos > $to->pos) {
            DB::table($this->table)->where('usr', $this->usr)->where('trg', $this->trg)->where('pos', '>=', $to->pos)->where('pos', '<', $this->pos)->increment('pos', +1);
        } else {
            DB::table($this->table)->where('usr', $this->usr)->where('trg', $this->trg)->where('pos', '<=', $to->pos)->where('pos', '>', $this->pos)->increment('pos', -1);
        }
        
        DB::table($this->table)->where('id', $this->id)->update([ 'pos' => $to->pos ]);
        return $this;
    }

    public function admURL(): string
    {
        return route('lootbox.content-block.block.base', [ 'trg' => $this->trg, 'usr' => $this->usr, 'grp' => $this->grp ]);
    }

    public function uploadsGalleryForHandle(string $handle): string
    {
        return view('lootbox::content-block.form.image-file-upload-gallery', [
            'images'    => $this->images($this->image_class::getPreviewSize()),
            'uploadURL' => url($this->admURL() . '/image'),
        ])->render();
    }

    public function previews(): Collection
    {
        return $this->image_class::where('usr', $this->id)->where('size', $this->image_class::getPreviewSize())->orderBy('pos', 'asc')->get();
    }

    public function images(int $size = 1): Collection
    {
        return $this->image_class::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function randomImage(int $size = 1): ?BaseImage
    {
        return $this->image_class::where('usr', $this->id)->where('size', $size)->orderByRaw('rand()')->first();
    }

    public function firstImage(int $size = 1): ?BaseImage
    {
        return $this->image_class::where('usr', $this->id)->where('size', $size)->orderBy('pos')->first();
    }

    public function countImages(): int
    {
        return $this->image_class::where('usr', $this->id)->where('size', 1)->count();
    }

    public function clearImages(): void
    {
        foreach ($this->images(1) as $image) {
            $image->delete();
        }
    }

    public function dropImages(array $ids = null): void
    {
        if ($ids && count($ids)) {
            foreach ($ids as $id) {
                $this->image_class::find($id)->delete();
            }
        }
    }

    public function reposImages(int $from_id, int $to_id): array
    {
        return $this->image_class::repos($from_id, $to_id);
    }

    public function quoteImage(int $size = 3): ?BaseImage
    {
        return $this->quote_image_class::where('usr', $this->id)->where('size', $size)->orderByRaw('rand()')->first();
    }

    public function quoteImages(int $size = 3): Collection
    {
        return $this->quote_image_class::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function clearQuoteImages(): void
    {
        foreach ($this->quoteImages(1) as $image) {
            $image->delete();
        }
    }

    public function videoImage(int $size = 3): ?BaseImage
    {
        return $this->video_image_class::where('usr', $this->id)->where('size', $size)->orderByRaw('rand()')->first();
    }

    public function videoImages(int $size = 3): Collection
    {
        return $this->video_image_class::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function clearVideoImages(): void
    {
        foreach ($this->videoImages(1) as $image) {
            $image->delete();
        }
    }

    public function blockView(&$config, array $vars = null): string
    {
        $data        = new \stdClass;
        $data->content   = false;
        $data->css   = false;
        $data->state = 0;

        if ($vars) {
            foreach ($vars as $k => $v) {
                $data->$k = $v;
            }
        }
        $css_classes = [];

        switch ($this->mode) {
            case 4:
                $data->code = $this->parseVideoCode();
                break;
        }

        $data->css = implode(' ', array_values($css_classes));
        $mode_tid = array_get($config->block_modes, $this->mode);
        return view(Arr::get($vars, 'view_prefix', 'lootbox::content-block.view.') . $mode_tid, [ 'block' => $this, 'data'  => $data, ])->render();
    }

    public function blockForm(&$config, $vars = null): string
    {
        $data = new \stdClass;
        $data->content = false;
        $data->css = false;
        $data->state = 1;

        $mode_tid = array_get($config->block_modes, $this->mode);
        return view(Arr::get($vars, 'view_prefix', 'lootbox::content-block.form.') . $mode_tid, [ 'block' => $this, 'data' => $data ])->render();
    }

    public function parseVideoCode(int $width = 600, int $height = 400): string
    {
        $mod = 'fill';
        
        if (is_string($this->code)) {
            return self::_parseVideoCode($this->code, $this->grp, $width, $height, $mod);
        }

        return '';
    }

    public static function _parseVideoCode(string $video_code, int $grp, int $width = 736, int $height = 414, string $mod = 'fill'): string
    {
        $video_mode = 1;
        if (strpos($video_code, 'vimeo.')) {
            $video_mode = 2;
        } elseif (strpos($video_code, 'youtube.')) {
            $video_mode = 3;
        } elseif (preg_match("/^[\d]+$/", $video_code)) {
            return '<iframe id="player_' . $grp . '" class="videoPlayer vimeoMarker" src="http://player.vimeo.com/video/' .
                $video_code . '?api=1&title=0&byline=0&portrait=0&color=233121&player_id=player_' . $grp . '" width="' . $width . '" height="' . $height .
                '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        } elseif (substr($video_code, 0, 6) == '[vimeo') {
            list($type,$clip_id,$width,$height) = explode('=', $video_code);
            return '<iframe id="player_' . $grp . '" class="videoPlayer vimeoMarker" src="http://player.vimeo.com/video/' . $clip_id .
                '?api=1&title=0&byline=0&portrait=0&color=233121&player_id=player_' . $grp . '" width="' . $width . '" height="' . $height .
                '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }

        switch ($video_mode) {
            case 3: // youtube
                $patterns = array("/(<iframe)/i");
                $replace = array("$1 id=\"player_" . $grp . "\" class=\"videoPlayer youtubeMarker\"");
                if (false===strpos($video_code, "enablejsapi=1")) {
                    $patterns[] = "/(src=\"[^\"]+)/i";
                    $replace[] = "$1?controls=0&wmode=opaque&enablejsapi=1&version=3&playerapiid=player_" . $grp;
                }
                break;
            case 2: // vimeo
                $patterns = array("/(<iframe)/i","/\?/i");
                $replace = array("$1 id=\"player" . $grp . "\" class=\"videoPlayer vimeoMarker\"","?player_id=player_" . $grp);
                if (false===strpos($video_code, "api=1")) {
                    $patterns[] = "/\?/i";
                    $replace[] = "?api=1";
                }
                break;
            case 1:
            default:
                break;
        }

        if (false===strpos($video_code, "width")) {
            $patterns[] = "/(<iframe)/i";
            $replace[] = "$1 width=\"" . $width . "\"";
        } else {
            $patterns[] = "/width=\"\d+\"/i";
            $replace[] = "width=\"" . $width . "\" data-width=\"" . $width . "\"";
        }

        if (false===strpos($video_code, "height")) {
            $patterns[] = "/(<iframe)/i";
            $replace[] .= "$1 height=\"" . $height . "\"";
        } else {
            $patterns[] = "/height=\"\d+\"/i";
            $replace[] = "height=\"" . $height . "\" data-height=\"" . $height . "\"";
        }

        $video_code = preg_replace($patterns, $replace, $video_code);

        return $video_code;
    }

    // protected static $_parsers = [
    //     self::YOUTUBE => [
    //         'id' => '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
    //         'player' => 'http://www.youtube.com/embed/:video_id',
    //         'image' => 'http://img.youtube.com/vi/:video_id/0.jpg',
    //     ],
    //     self::VIMEO => [
    //         'id' => '/[http|https]+:\/\/(?:www\.|)(?:player\.|)vimeo\.com\/(?:video\/|)([a-zA-Z0-9_\-]+)(&.+)?/i',
    //         'player' => 'http://player.vimeo.com/video/:video_id',
    //         'image' => '',
    //     ],
    //     self::RUTUBE => [
    //         'id' => '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i',
    //         'player' => 'http://rutube.ru/video/embed/:video_id',
    //         'image' => '',
    //     ],
    // ];
    // public function get_video_id($value) {
    //     if (is_null($this->_video_id)) {
    //         $value = trim($value);

    //         $parser = Arr::get(self::$_parsers, $this->_video_type);

    //         $this->_video_id = self::parse_video_id(Arr::get($parser, 'id'), $value);
    //         $this->_video_player = self::parse_video_player(Arr::get($parser, 'player'), $this->_video_id);
    //         $this->_video_image = self::parse_video_image(Arr::get($parser, 'image'), $this->_video_id);
    //     }

    //     return (string)$this->_video_id;
    // }
}
