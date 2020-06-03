<?php

namespace Psytelepat\Lootbox\Http\Controllers\Publicator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Publicator\PublicatorCategory as Category;

/**
 * Контроллер категория публикаций
 */
class PublicatorAdminCategoryController extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\Publicator\PublicatorCategory';
    protected $FORM = 'lootbox::publicator.category-form';
    protected $ADMIN_ROUTE = 'publicator.category';
    protected $LANG_ROUTE = 'lootbox::publicator.categories';

    public static function validationRules(string $mode, Model $model = null): array
    {
        return [
            'slug' => 'required',
            'title' => 'required',
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('title')->sortable()->searchable()->link($link);
        $list->column('is_published')->title('')->html(\Closure::fromCallable(self::class.'::formatIsPublished'));
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        switch ($mode) {
            case 'create':
                $model->usr = 1;
                $grp = intval($request->input('grp'));
                $pos = intval($request->input('pos'));
                $lng = intval($request->input('lng', Util::localeid()));

                $model->lng = $lng;
                $model->grp = $grp ? $grp : Util::newGrp(Category::class);
                $model->pos = $pos ? $pos : Util::newPos(Category::class);

                $model->slug = Util::unifySlug(Category::class, Util::slug($request->input('slug')));
                $model->title = $request->input('title');
                $model->is_published = 1;
                break;
            case 'edit':
                $model->slug = Util::unifySlug(Category::class, Util::slug($request->input('slug')), $model->id);
            
                $model->title = $request->input('title');
                $model->description = $request->input('description');
                $model->content = $request->input('content');
                break;
            default:
                throw new \Exception('Invalid mode');
                break;
        }

        return true;
    }
}
