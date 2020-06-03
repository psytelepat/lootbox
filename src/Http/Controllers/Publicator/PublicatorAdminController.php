<?php

namespace Psytelepat\Lootbox\Http\Controllers\Publicator;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Publicator\PublicatorPost as Post;
use Psytelepat\Lootbox\Publicator\PublicatorCategory as Category;
use Psytelepat\Lootbox\Publicator\PublicatorAuthor as Author;

use View;
use DB;

/**
 * Контроллер публикаций
 */
class PublicatorAdminController extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\Publicator\PublicatorPost';
    protected $FORM = 'lootbox::publicator.post-form';
    protected $ADMIN_ROUTE = 'publicator.post';
    protected $LANG_ROUTE = 'lootbox::publicator.posts';

    private function pageHeading(string $mode, Model $model = null): Renderable
    {
        return view('lootbox::admin.page-heading', [
            'title' => $this->MODEL_CLASS::adminFormTitle($mode, $model),
            'breadcrumbs' => [
                [
                    'name' => __('lootbox::publicator.publicator'),
                    'route' => route('lootbox.publicator.index'),
                ],
                [
                    'name' => __($this->LANG_ROUTE),
                    'route' => route('lootbox.'.$this->ADMIN_ROUTE.'.index'),
                ],
            ]
        ]);
    }

    public static function validationRules(string $mode, Model $model = null): array
    {
        return [
            'slug' => 'required',
            'title' => 'required',
            'category_id' => 'exists:publicator_category,id',
        ];
    }

    public static function listColumns(&$list): void
    {
        $lng = request()->input('lng');
        $category_id = request()->input('category_id');
        $author_id = request()->input('author_id');
        $tag = request()->input('tag');

        $list->appends(request()->only([ 'category_id', 'author_id', 'tag', ]))
        ->query(function ($query) use ($lng, $category_id, $author_id, $tag) {
            if ($lng) {
                $query->where('lng', $lng);
            }
            if ($category_id) {
                $query->where('category_id', $category_id);
            }
            if ($author_id) {
                $query->where('author_id', $author_id);
            }
            if ($tag) {
                $query->whereExists(function ($query) use ($tag) {
                    $query->select('taggable_id')
                        ->from('tagging_tagged')
                        ->whereRaw('tagging_tagged.taggable_id = publicator_post.id')
                        ->where('taggable_type', Post::class)
                        ->where('tag_slug', $tag);
                });
            }
        });

        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');

        if ($category_id) {
            $list->column('id')->sortable()->link($link);
            $list->column('pos')->sortable(true, 'desc')->html(\Closure::fromCallable(self::class.'::formatPos'));
        } else {
            $list->column('id')->sortable(true, 'desc')->link($link);
        }

        $list->column('lng')->sortable()->html(function ($model, $column) {
            return strtoupper(Util::localetid($model->lng));
        });
        $list->column('title')->sortable()->searchable()->link($link);
        $list->column('is_published')->title('')->html(\Closure::fromCallable(self::class.'::formatIsPublished'));
        $list->column('created_at')->sortable();

        View::share('additional_thead_controls', view('lootbox::publicator.post-thead-controls', [ 'table' => $list ])->render());

        $list->tableClasses(['sortable']);
    }

    public static function gatherFormData(string $mode, Model $model = null): array
    {
        $data = parent::gatherFormData($mode, $model);
        switch ($mode) {
            case 'edit':
                $data['tags_typeahead_source'] = implode(',', Post::existingTags()->pluck('name')->all());
                $data['similars'] = config('publicator.with_similars') ? $model->similars(true): null;
                // fall-through
            case 'create':
                $data['categories'] = config('publicator.with_categories') ? Category::where('lng', $model ? $model->lng : Util::localeid())->get() : null;
                $data['authors'] = config('publicator.with_authors') ? Author::where('lng', $model ? $model->lng : Util::localeid())->get() : null;
                break;
            default:
                break;
        }
        return $data;
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        $created_at_date = $request->input('created_at_date');
        $created_at_time = $request->input('created_at_time');

        if ($created_at_time && $created_at_date) {
            list($d,$m,$Y) = explode('.', $created_at_date);
            list($H,$i) = explode(':', $created_at_time);
            $model->created_at = \Carbon\Carbon::create($Y, $m, $d, $H, $i, 0)->format('Y-m-d H:i:s');
        }

        switch ($mode) {
            case 'create':
                $grp = intval($request->input('grp'));
                $pos = intval($request->input('pos'));
                $lng = intval($request->input('lng', Util::localeid()));

                $model->usr = 1;
                $model->lng = $lng;
                $model->grp = $grp ? $grp : Util::newGrp(Post::class);
                $model->pos = $pos ? $pos : Util::newPos(Post::class);

                $model->slug = Util::unifySlug(Post::class, Util::slug($request->input('slug')), 0, [ 'tag', 'category', 'rate' ]);
                $model->title = $request->input('title');
                $model->description = $request->input('description');
                $model->content = $request->input('content');
                $model->is_published = 0;
                $model->category_id = (int)$request->input('category_id');
                $model->author_id = (int)$request->input('author_id');

                $model->handelSEOData($request);
                $model->save();

                if ($tags = $request->input('tags')) {
                    $model->retag(explode(',', $tags));
                }
                break;
            case 'edit':
                $model->slug = Util::unifySlug(Post::class, Util::slug($request->input('slug')), $model->id, [ 'tag', 'category', 'rate' ]);
                $model->title = $request->input('title');
                $model->description = $request->input('description');
                $model->content = $request->input('content');
                $model->category_id = (int)$request->input('category_id');
                $model->author_id = (int)$request->input('author_id');
                $model->is_published = $request->input('is_published') ? 1 : 0;

                $model->handelSEOData($request);
                $model->save();

                if ($tags = $request->input('tags')) {
                    $model->retag(explode(',', $tags));
                } else {
                    $model->untag();
                }

                // if( config('publicator.with_categories') ){
                //     $model->resetCategories( $request->input('categories') );
                // }

                // if( config('publicator.with_similars') ){
                //     $model->resetSilimars( $request->input('similars') );
                // }

                Post::where('grp', $model->grp)->where('lng', '<>', $model->lng)
                    ->update([ 'slug' => $model->slug, 'pos' => $model->pos ]);
                break;
            default:
                throw new \Exception('Invalid mode');
                break;
        }

        return true;
    }

    public function tags(Request $request)
    {
        $list = (new \Okipa\LaravelTable\Table)->model(\Conner\Tagging\Model\Tag::class)->query(function ($query) {
            $query->whereExists(function ($query) {
                $query->select('id')
                    ->from('tagging_tagged')
                    ->where('taggable_type', (new Post)->getMorphClass())
                    ->whereRaw('tagging_tagged.tag_slug = tagging_tags.slug')
                    ->limit(1);
            });
        })->routes([
            'index' => [ 'name' => 'lootbox.publicator.tags.index' ],
        ]);
        
        $list->column('slug')->sortable();
        $list->column('name')->sortable()->link(function ($model, $column) {
            return route('lootbox.publicator.post.index', [ 'tag' => $model->slug ]);
        });
        $list->column('count')->sortable(true, 'desc')->title(__('validation.attributes.usages'));
        
        return $this->template->push('content', $list->render());
    }
}
