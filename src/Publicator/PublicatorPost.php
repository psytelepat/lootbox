<?php

namespace Psytelepat\Lootbox\Publicator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
use Lang;
use Validator;
use ContentBlock;

use Conner\Tagging\Model\Tagged;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Publicator\SEOImage;
use Psytelepat\Lootbox\Publicator\PublicatorCover;
use Psytelepat\Lootbox\Publicator\PublicatorCategory;
use Psytelepat\Lootbox\Publicator\PublicatorAuthor;
use Psytelepat\Lootbox\Publicator\PublicatorCategoryMap;
use Psytelepat\Lootbox\Publicator\PublicatorSimilarMap;
use Psytelepat\Lootbox\Publicator\PublicatorRatingMap;
use Psytelepat\Lootbox\ContentBlock\ContentBlockModel;

class PublicatorPost extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    use \Conner\Tagging\Taggable;
    use \Sofa\Eloquence\Eloquence;
    use \Psytelepat\Lootbox\Publicator\LanguageLinks;

    protected $cover_class = PublicatorCover::class;

    public function getCoverClass()
    {
        return $this->cover_class;
    }

    public function setCoverClass($cover_class)
    {
        $this->cover_class = $cover_class;
        return $this;
    }

    protected $table = 'publicator_post';
    protected $fillable = [
    'slug',
    'title',
    'description',
    'content',
    ];

    protected $searchableColumns = [
      'title' => 10,
      'description' => 10,
      'content' => 10,
      'contentBlock.title' => 5,
      'contentBlock.description' => 5,
      'contentBlock.content' => 10,
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($item) {
            $item->untag();

            $item->categories()->detach();
            $item->similars()->detach();

            foreach ($item->covers(1) as $image) {
                $image->delete();
            }
            foreach ($item->seo_images(1) as $image) {
                $image->delete();
            }
            ContentBlock::dropContentFor(1, $item->grp);
        });
    }

    public function contentBlock()
    {
        return $this->hasMany(ContentBlockModel::class, 'usr', 'grp')->where('trg', 1);
    }

    public function contentBlockEditor()
    {
        return ContentBlock::embed(1, $this->grp);
    }


    public function tagsString()
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            if ($tag) {
                $tags[] = $tag->name;
            }
        }
        return implode(',', $tags);
    }

    public static function tagsStringAvailable()
    {
        $tags = [];
        foreach (self::existingTags() as $tag) {
            $tags[] = $tag->name;
        }
        return implode(',', $tags);
    }

    public static function existingTagsCount()
    {
        $result = Tagged::distinct()
        ->join('tagging_tags', 'tag_slug', '=', 'tagging_tags.slug')
        ->where('taggable_type', '=', (new static)->getMorphClass())
        ->get(array('tag_slug as slug'));

        return $result->count();
    }

    public static function existingTagsByCountWithLimit(int $limit = 5)
    {
        return Tagged::distinct()
        ->join('tagging_tags', 'tag_slug', '=', 'tagging_tags.slug')
        ->where('taggable_type', '=', (new static)->getMorphClass())
        ->orderBy('count', 'DESC')
        ->limit($limit)
        ->get(array('tag_slug as slug', 'tag_name as name', 'tagging_tags.count as count'));
    }

    public function parsedPos()
    {
        return Util::parse_date($this->pos);
    }

    public function parsedCreated()
    {
        return Util::parse_date(strtotime($this->created_at));
    }

    public function covers($size = 1)
    {
        return $this->cover_class::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function cover($size = 1)
    {
        return $this->cover_class::where('usr', $this->id)->where('size', $size)->orderByRaw('rand()')->first();
    }

    public function randomCover($size = 1)
    {
        return $this->cover($size);
    }

    public function seo_images($size = 1)
    {
        return SEOImage::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

  // SEO DATA
    public function handelSEOData(Request $request): void
    {
        $title       = $request->input('seo_title');
        $description = $request->input('seo_description');
        $keywords    = $request->input('seo_keywords');

        if (isset($title)) {
            $this->seo_title = $title;
        }
        if (isset($description)) {
            $this->seo_description = $description;
        }
        if (isset($keywords)) {
            $this->seo_keywords = $keywords;
        }
    }



  // CATEGORIES
    public function updateAllCategoriesUsage()
    {
        if ($links = PublicatorCategoryMap::where('post_grp', $this->grp)->where('lng', Util::localeid())->get()) {
            foreach ($links as $link) {
                PublicatorCategory::updateUsage($link->category_grp);
            }
        }
    }

    public function similars()
    {
        return $this->belongsToMany(PublicatorPost::class, 'publicator_similar_map', 'post_grp', 'similar_grp', 'grp', 'grp');
    }

    public function category()
    {
        return $this->belongsTo(PublicatorCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(PublicatorAuthor::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(PublicatorCategory::class, 'publicator_category_map', 'post_grp', 'category_grp', 'grp', 'grp');
    }

    public function prevPost(bool $cycle = true): PublicatorPost
    {
        $query = self::where('created_at', '<', $this->pos)->where('is_published', true)->where('id', '<>', $this->id)->where('lng', Util::localeid());

        if (config('publicator.with_categories')) {
            $query->where('category_id', $this->category_id);
        }

        $prev_post = $query->orderByRaw('pos desc')->first();

        if ($cycle && !$prev_post) {
            $prev_post = self::where('is_published', true)->where('lng', Util::localeid())->orderBy('created_at', 'desc')->first();
        }

        return $prev_post;
    }

    public function nextPost(bool $cycle = true): PublicatorPost
    {
        $query = self::where('created_at', '>', $this->pos)->where('is_published', '>', 0)->where('lng', Util::localeid());
    
        if (config('publicator.with_categories')) {
            $query->where('category_id', $this->category_id);
        }

        $next_post = $query->orderByRaw('pos asc')->first();

        if ($cycle && !$next_post) {
            $next_post = self::where('is_published', true)->where('lng', Util::localeid())->orderBy('created_at', 'asc')->first();
        }

        return $next_post;
    }

    public static function populatePopular()
    {
        $items = self::where('lng', Util::localeid())->where('rating', '>', 0)->orderByRaw('`rating` desc')->take(3)->get();
        if ($items && count($items)) {
            return $items;
        }
        return null;
    }

    public function reposTo(PublicatorPost $to): PublicatorPost
    {
        if ($this->pos > $to->pos) {
            DB::table($this->table)->where('pos', '>=', $to->pos)->where('pos', '<', $this->pos)->increment('pos', +1);
        } else {
            DB::table($this->table)->where('pos', '<=', $to->pos)->where('pos', '>', $this->pos)->increment('pos', -1);
        }
        
        DB::table($this->table)->where('id', $this->id)->update([ 'pos' => $to->pos ]);
        return $this;
    }

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::publicator.posts_new');
            break;
            case 'delete':
                return __('lootbox::publicator.posts_delete', [ 'title' => $model->title ]);
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
                return route('lootbox.publicator.post.create');
            break;
            case 'edit':
                return route('lootbox.publicator.post.edit', $model);
            break;
            case 'delete':
                return route('lootbox.publicator.post.delete', $model);
            break;
            case 'index':
                return route('lootbox.publicator.post.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
