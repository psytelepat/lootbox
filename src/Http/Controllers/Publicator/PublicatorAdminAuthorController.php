<?php

namespace Psytelepat\Lootbox\Http\Controllers\Publicator;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Publicator\PublicatorAuthor as Author;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

use Psytelepat\Lootbox\Util;

/**
 * Контроллер авторов публикаций
 */
class PublicatorAdminAuthorController extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\Publicator\PublicatorAuthor';
    protected $FORM = 'lootbox::publicator.author-form';
    protected $ADMIN_ROUTE = 'publicator.author';
    protected $LANG_ROUTE = 'lootbox::publicator.authors';

    public static function validationRules(string $mode, $model = null): array
    {
        return [
            'name' => 'required',
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('name')->sortable()->searchable()->link($link);
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        switch ($mode) {
            case 'create':
                $grp = intval($request->input('grp'));
                $pos = intval($request->input('pos'));
                $lng = intval($request->input('lng', Util::localeid()));

                $model->usr = 1;
                $model->lng = $lng;
                $model->grp = $grp ? $grp : Util::newGrp(Author::class);
                $model->pos = $pos ? $pos : Util::newPos(Author::class);

                if ($slug = $request->input('slug')) {
                    $model->slug = Util::unifySlug(Author::class, Util::slug($slug));
                }

                $model->name = $request->input('name');
                $model->description = $request->input('description');
                $model->content = $request->input('content');

                $model->ig_url = $request->input('ig_url');
                $model->tw_url = $request->input('tw_url');
                $model->fb_url = $request->input('fb_url');
                $model->vk_url = $request->input('vk_url');
                $model->yt_url = $request->input('yt_url');
                break;
            case 'edit':
                if ($slug = $request->input('slug')) {
                    $model->slug = Util::unifySlug(Author::class, Util::slug($slug), $model->id);
                }

                $model->name = $request->input('name');
                $model->description = $request->input('description');
                $model->content = $request->input('content');

                $model->ig_url = $request->input('ig_url');
                $model->tw_url = $request->input('tw_url');
                $model->fb_url = $request->input('fb_url');
                $model->vk_url = $request->input('vk_url');
                $model->yt_url = $request->input('yt_url');
                break;
            default:
                throw new \Exception('Invalid mode');
                break;
        }

        return true;
    }
}
