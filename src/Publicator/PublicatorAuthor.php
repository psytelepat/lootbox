<?php

namespace Psytelepat\Lootbox\Publicator;

use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Util;

use Psytelepat\Lootbox\Publicator\PublicatorPost;
use Psytelepat\Lootbox\Publicator\PublicatorAuthorAvatar;

class PublicatorAuthor extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    use \Psytelepat\Lootbox\Publicator\LanguageLinks;

    protected $table = 'publicator_author';
    protected $fillable = [
        'trg',
        'usr',
        'grp',
        'lnk',
        'lng',
        'pos',
        'slug',
        'name',
        'description',
        'content',
        'ig_url',
        'tw_url',
        'fb_url',
        'vk_url',
        'yt_url',
    ];

    public function posts()
    {
        return $this->hasMany(PublicatorPost::class, 'category_id');
    }

    public function url(string $mode = 'view', $param = false)
    {
        switch ($mode) {
            case 'adm':
            case 'edit':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'author','edit',$this->grp]);
            break;
            case 'drop':
            case 'delete':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'author','delete',$this->grp]);
            break;
            case 'copy':
            case 'translate':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'author','edit',$this->grp,'translate',$param]);
            break;
            case 'view':
                return Util::userURL($this->lng, [config('blog.adm_pfx'),'author',$this->tid]);
            break;
        }

        return null;
    }

    public function avatars(int $size = 1)
    {
        return PublicatorAuthorAvatar::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function randomAvatar(int $size = 1)
    {
        return PublicatorAuthorAvatar::where('usr', $this->id)->where('size', $size)->orderByRaw('rand()')->first();
    }

    public static function adminFormTitle(string $mode = null, $model = null) : string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::publicator.authors_new');
            break;
            case 'delete':
                return __('lootbox::publicator.authors_delete', [ 'name' => $model->name ]);
            break;
            default:
                return $model->name;
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null) : string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.publicator.author.'.$mode);
            break;
            case 'edit':
                return route('lootbox.publicator.author.'.$mode, $model);
            break;
            case 'delete':
                return route('lootbox.publicator.author.'.$mode, $model);
            break;
            case 'index':
                return route('lootbox.publicator.author.'.$mode);
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
