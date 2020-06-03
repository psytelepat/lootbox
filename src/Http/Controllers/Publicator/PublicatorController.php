<?php

namespace Psytelepat\Lootbox\Http\Controllers\Publicator;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Psytelepat\Lootbox\Publicator\Http\Requests;
use Psytelepat\Lootbox\Publicator\Http\Controllers\AbstractController as BaseController;

use Illuminate\Pagination\Paginator;

use App;
use URL;
use Lang;
use Validator;
use Redirect;
use Psytelepat\Lootbox\Publicator\Util;
use Psytelepat\Lootbox\Publicator\BlogPost;
use Psytelepat\Lootbox\Publicator\SitePage;
use Psytelepat\Lootbox\Publicator\Settings;
use Psytelepat\Lootbox\Publicator\BlogCategory;
use Psytelepat\Lootbox\Publicator\PrettyPaginator;
use Psytelepat\Lootbox\Publicator\BlogRatingMap;
use SEO;

/**
 * Контроллер публикатора
 */
class PublicatorController extends BaseController
{
    private static $perpage = 20;

    // LIST
    public function index(Request $request, $page = false): Renderable
    {
        if (!$page) {
            $page = 1;
        }

        SitePage::populate('blog');
        if ($page > 1) {
            SEO::metatags()->addMeta('robots', 'noindex,nofollow');
        }

        $totalItems = BlogPost::count();
        $items = BlogPost::where('dsp', '>', '0')->where('lng', Util::localeid())->orderByRaw('pos desc')->take(self::$perpage)->skip(($page-1) * self::$perpage)->get();

        $paginator = new PrettyPaginator($items->toArray(), $totalItems, self::$perpage, $page);
        $paginator->setPath('/' . config('blog.pfx'));

        return view('section.blog', [
            'SECTION'   => config('blog.section_tid'),
            'items'     => $items,
            'paginator' => $paginator,
            'popular'   => BlogPost::populatePopular(),
        ]);
    }

    public function tag(Request $request, $tag_slug, $page = false): Renderable
    {
        if (!$page) {
            $page = 1;
        }

        SitePage::populate('blog');
        SEO::metatags()->addMeta('robots', 'noindex,nofollow');


        $items = null;
        $totalItems = 0;
        $tag = \Conner\Tagging\Model\Tag::where('slug', $tag_slug)->first();
        if (!$tag) {
            return response()->view('errors.404', array(), 404);
        }

        $totalItems = BlogPost::withAnyTag([$tag_slug])->where('dsp', '>', '0')->where('lng', Util::localeid())
                    ->count();
        $items = BlogPost::withAnyTag([$tag_slug])->where('dsp', '>', '0')->where('lng', Util::localeid())->orderByRaw('pos desc')
                    ->take(self::$perpage)->skip(($page-1) * self::$perpage)->get();

        $paginator = new PrettyPaginator($items->toArray(), $totalItems, self::$perpage, $page);
        $paginator->setPath('/' . config('blog.pfx') . '/tag/' . $tag_slug);

        return view('section.blog', [
            'SECTION'   => config('blog.section_tid'),
            'items'     => $items,
            'paginator' => $paginator,
            'popular'   => BlogPost::populatePopular(),
            'foundBox'  => str_replace(array('%TAG%','%COUNT%'), array($tag->name, $totalItems), trans('blog.tag_found')),
        ]);
    }



    // CATEGORY
    public function category(Request $request, string $category_tid, $page = false): Renderable
    {
        if (!$page) {
            $page = 1;
        }

        SitePage::populate('blog');
        SEO::metatags()->addMeta('robots', 'noindex,nofollow');

        if (! ( $category = BlogCategory::where('tid', $category_tid)->where('dsp', '>', '0')->where('lng', Util::localeid())->first() )) {
            return response()->view('errors.404', array(), 404);
        }

        $totalItems = $items = BlogPost::whereIn('grp', function ($query) use ($category) {
            $query->from('blog_category_map')->select('post_grp')->where('category_grp', $category->grp)->where('lng', Util::localeid());
        })->where('dsp', '>', '0')->where('lng', Util::localeid())->count();

        $items = BlogPost::whereIn('grp', function ($query) use ($category) {
            $query->from('blog_category_map')->select('post_grp')->where('category_grp', $category->grp)->where('lng', Util::localeid());
        })->where('dsp', '>', '0')->where('lng', Util::localeid())->orderByRaw('pos desc')->take(self::$perpage)->skip(($page-1) * self::$perpage)->get();

        $paginator = new PrettyPaginator($items->toArray(), $totalItems, self::$perpage, $page);
        $paginator->setPath('/' . config('blog.pfx') . '/category/' . $category->tid);

        return view('section.blog', [
            'SECTION'   => config('blog.section_tid'),
            'items'     => $items,
            'paginator' => $paginator,
            'activeCategory' => $category->tid,
            'popular'   => BlogPost::populatePopular(),
            'foundBox'  => str_replace(array('%CATEGORY%','%COUNT%'), array($category->ttl, $totalItems), trans('blog.category_found')),
        ]);
    }



    // SEARCH
    public function search(Request $request, string $search = null, bool $page = false): Renderable
    {
        if (!$page) {
            $page = 1;
        }

        SitePage::populate('blog');
        SEO::metatags()->addMeta('robots', 'noindex,nofollow');

        if (!$search) {
            $search = $request->input('search');
        }

        $totalItems = BlogPost::search($search)->where('dsp', '>', '0')->where('lng', Util::localeid())->count();
        $items = BlogPost::search($search)->where('dsp', '>', '0')->where('lng', Util::localeid())->take(self::$perpage)->skip(($page-1) * self::$perpage)->get();

        $paginator = new PrettyPaginator($items->toArray(), $totalItems, self::$perpage, $page);
        $paginator->setPath('/' . config('blog.pfx') . '/search/' . $search);

        return view('section.blog', [
            'SECTION'   => config('blog.section_tid'),
            'search_string' => $search,
            'items'     => $items,
            'paginator' => $paginator,
            'popular'   => BlogPost::populatePopular(),
            'foundBox'  => str_replace(array('%COUNT%'), array($totalItems), trans($totalItems ? 'blog.search_found' : 'blog.search_not_found')),
        ]);
    }

    public function autocomplete(Request $request, int $grp = null): array
    {
        $validator = Validator::make($request->all(), [
            'autocomplete' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([ 'error' => 'invalid data', ]);
        }

        $search = $request->input('autocomplete');
        $posts = BlogPost::select(['ttl','grp'])->search($search)->where('lng', Util::localeid())->where('grp', '<>', $grp)->orderByRaw('pos desc')->get();
        $items = [];

        foreach ($posts as $post) {
            $items[] = (object)[
                'ttl' => $post->ttl,
                'grp' => $post->grp,
            ];
        }

        return [
            'success' => 1,
            'items' => $items,
        ];
    }



    // VIEW
    public function view(Request $request, string $tid): Renderable
    {
        if (! ( $item = BlogPost::where('tid', $tid)->where('dsp', '>', '0')->where('lng', Util::localeid())->first() )) {
            return response()->view('errors.404', array(), 404);
        }

        SitePage::populate('blog');

        if ($item->seo_title) {
            SEO::setTitle($item->seo_title);
        }
        if ($item->seo_description) {
            SEO::setDescription($item->seo_description);
        }
        if ($item->seo_keywords) {
            SEO::metatags()->setKeywords($item->seo_keywords);
        }

        SEO::opengraph()->setUrl(URL::to($item->url('view')));
        if ($cover = $item->randomCover(2)) {
            SEO::opengraph()->addImages([ URL::to($cover->url()) ]);
        }

        $item->views = $item->views + 1;
        $item->save();

        $my_marks = $request->session()->get('blog.marks');

        return view('section.blog', [
            'SECTION'   => config('blog.section_tid'),
            'post'      => $item,
            'canRate'   => !( isset($my_marks) && is_array($my_marks) && in_array($item->grp, $my_marks) ),
            'popular'   => BlogPost::populatePopular(),
        ]);
    }

    public function rate(Request $request, string $tid): array
    {
        if (! ( $item = BlogPost::where('tid', $tid)->where('dsp', '>', '0')->where('lng', Util::localeid())->first() )) {
            return response()->json([ 'error' => 'UNABLE TO FIND POST', 'tid' => $tid ]);
        }

        $validator = Validator::make($request->all(), [
            'mark' => 'required|integer|in:1,2,3,4,5',
        ]);

        if ($validator->fails()) {
            return response()->json([ 'error' => 'INVALID DATA', ]);
        }

        $item->rating = BlogRatingMap::addMark($item, intval($request->input('mark')));
        $item->save();

        $request->session()->push('blog.marks', $item->grp);

        return [
            'success' => 1,
            'mark' => $item->rating
        ];
    }

    public function feed(Request $request)
    {
        // create new feed
        $feed = App::make("feed");

        // multiple feeds are supported
        // if you are using caching you should set different cache keys for your feeds

        // cache the feed for 60 minutes (second parameter is optional)
        // $feed->setCache(60, 'blogFeedKey');

        // check if there is cached feed and build new only if is not
        if (!$feed->isCached()) {
           // creating rss feed with our most recent 20 posts
            $posts = \App\Blog\BlogPost::orderBy('created_at', 'desc')->take(20)->get();

           // set your feed's title, description, link, pubdate and language
            $feed->title = trans('common.siteTitle');
            $feed->description = trans('common.siteTitle');
            $feed->logo = URL::to('/img/favicon/favicon-96x96.png');
            $feed->link = url(config('blog.pfx') . '/feed');
            $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
            $feed->pubdate = $posts[0]->created_at;
            $feed->lang = Lang::locale();
            $feed->setShortening(true); // true or false
            $feed->setTextLimit(100); // maximum length of description text

            foreach ($posts as $post) {
                // set item's title, author, url, pubdate, description, content, enclosure (optional)*
                $feed->add($post->ttl, trans('common.siteTitle'), URL::to($post->url('view')), $post->created_at, $post->dsc, false);
            }
        }

        // first param is the feed format
        // optional: second param is cache duration (value of 0 turns off caching)
        // optional: you can set custom cache key with 3rd param as string
        return $feed->render('atom');

        // to return your feed as a string set second param to -1
        // $xml = $feed->render('atom', -1);
    }
}
