<?php

namespace Psytelepat\Lootbox\Publicator;

use Illuminate\Database\Eloquent\Model;

use DB;

use Psytelepat\Lootbox\Util;

use Psytelepat\Lootbox\Publicator\PublicatorPost;
use Psytelepat\Lootbox\Publicator\PublicatorCategoryMap;

class PublicatorCategory extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    use \Psytelepat\Lootbox\Publicator\LanguageLinks;

    protected $table = 'publicator_category';
    protected $fillable = [
        'trg',
        'usr',
        'grp',
        'lnk',
        'lng',
        'pos',
        'slug',
        'title',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'description',
        'content',
        'usage',
        'is_published',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($item) {
            PublicatorCategoryMap::where('category_grp', $item->grp)->where('lng', $item->lng)->delete();
        });
    }
    
    /**
     * Обновление статистики использования данной категории
     *
     * @param  int $grp
     * @return void
     */
    public static function updateUsage(int $grp): void
    {
        if ($category = self::where('grp', $grp)->where('lng', Util::localeid())->first()) {
            $category->usage = PublicatorCategoryMap::where('category_grp', $category->grp)->whereExists(function ($query) {
                $query->select(DB::raw(1))
                ->from('blog_post')
                ->where('dsp', '>', 0)
                ->whereRaw('blog_post.grp = blog_category_map.post_grp');
            })->count();
            $category->save();
        }
    }

    public function url(string $mode = 'view', bool $param = false)
    {
        switch ($mode) {
            case 'adm':
            case 'edit':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'category','edit',$this->grp]);
            break;
            case 'drop':
            case 'delete':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'category','delete',$this->grp]);
            break;
            case 'copy':
            case 'translate':
                return Util::adminURL($this->lng, [config('blog.adm_pfx'),'category','edit',$this->grp,'translate',$param]);
            break;
            case 'view':
                return Util::userURL($this->lng, [config('blog.adm_pfx'),'category',$this->tid]);
            break;
        }

        return null;
    }

    public static function byUsage(int $take = 6)
    {
        return self::where('lng', Util::localeid())->where('usage', '>', 0)->orderByRaw('`usage` desc')->take($take)->get();
    }

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::publicator.categories_new');
            break;
            case 'delete':
                return __('lootbox::publicator.categories_delete', [ 'title' => $model->title ]);
            break;
            default:
                return $model->title;
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null): string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.publicator.category.create');
            break;
            case 'edit':
                return route('lootbox.publicator.category.edit', $model);
            break;
            case 'delete':
                return route('lootbox.publicator.category.delete', $model);
            break;
            case 'index':
                return route('lootbox.publicator.category.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
