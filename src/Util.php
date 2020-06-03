<?php

namespace Psytelepat\Lootbox;

use App;
use URL;
use Lang;
use Validator;
use Illuminate\Http\Request;

class Util
{
    public static $localesByTid;
    public static $localesById;

    public static $month = [
        'ru' => ['месяц','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'],
        'en' => ['month','january','february','march','april','may','june','july','august','september','october','november','december'],
    ];

    public static $months = array(
        'ru' => ['месяц','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'],
        'en' => ['month','january','february','march','april','may','june','july','august','september','october','november','december'],
    );

    public static $ru2en = array(
        'from'      => [' ','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ы','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ы','Э','Ю','Я'],
        'to'        => ['_','a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sh','','','i','e','u','ya','a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sh','','','i','e','u','ya'],
        'remove'    => ['`','~','!','@','#','$','%','^','&','*','(',')','[',']',':',';','\'','"','.',',','|','/','\\','?','<','>','{','}','=','+']
    );

    public static function init(): void
    {
        self::$localesByTid = config('app.locales');
        foreach (self::$localesByTid as $k => $v) {
            self::$localesById[$v['id']] = $v;
        }
    }

    public static function domain(int $localeid): string
    {
        return getenv('DOMAIN'.$localeid);
    }

    public static function locale_choices()
    {
        $locale_choices = [];
        foreach (self::$localesById as $id => $locale) {
            $locale_choices[$id] = $locale['title'];
        }
        return $locale_choices;
    }

    public static function localeid(string $tid = null): int
    {
        if (!$tid) {
            $tid = Lang::locale();
        }
        return isset(self::$localesByTid[$tid]) ? intval(self::$localesByTid[$tid]['id']): null;
    }

    public static function localetid(int $id = null): string
    {
        if (!$id) {
            return Lang::locale();
        }
        return isset(self::$localesById[$id]) ? self::$localesById[$id]['tid'] : null;
    }

    public static function parse_date(int $timestamp)
    {
        $parsed_date = date('j', $timestamp) . ' ' . Util::$months[Lang::getLocale()][intval(date('n', $timestamp))];
        // if( date('Y',$timestamp) !== date('Y') )
            $parsed_date .= ' ' . date('Y', $timestamp);
        return $parsed_date;
    }

    public static function za($var, int $len = 2, bool $pos = true, string $char = '0'): string
    {
        $nulls=($len-strlen($var)>=0)?str_repeat($char, $len-strlen($var)):'';
        return ($pos?$nulls.$var:$var.$nulls);
    }

    public static function translit(string $s, bool $removeSpecialChars = true, bool $removeTags = true): string
    {
        $s = str_replace(self::$ru2en['from'], self::$ru2en['to'], strtolower($s));
        
        if ($removeTags) {
            $s=preg_replace("/<[^>]*>/", "", $s);
        }
        
        if ($removeSpecialChars) {
            $s=str_replace(self::$ru2en['remove'], '', $s);
        }

        return $s;
    }

    public static function slug(string $tid): string
    {
        return(preg_replace("/[^0-9a-zA-Z\.\-\_]/", "", self::translit($tid)));
    }

    public static function uni(bool $repeat = false): int
    {
        static $uni = 1;
        return $repeat ? ( $uni - 1 ): $uni++;
    }

    public static function req2item(Request &$request, &$item, array $fields)
    {
        foreach ($fields as $field) {
            $item->$field = $request->input($field);
        }
    }



    public static function parseVideoCode(string $video_code, int $grp, int $width = 736, int $height = 414, string $mod = 'fill'): string
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

    public static function parseTime(int $time, bool $withTime = false, bool $withYear = false): string
    {
        $o = '';
        $today = mktime(0, 0, 0);
        $tomorrow = $today + (60 * 60 * 24);
        $yesterday = $today - (60 * 60 * 24);

        if ($time >= $today && $time < $tomorrow) {
            return trans('string.today');
        }

        if ($time >= $yesterday && $time < $today) {
            return trans('string.yesterday');
        }

        $d = intval(date('j', $time));
        $m = intval(date('n', $time));
        $Y = intval(date('Y', $time));

        $o .= $d . ' ' . Util::$months[Lang::locale()][$m] . ($withYear || ($Y != intval(date('Y'))) ? ' ' . $Y : '');

        if ($withTime) {
            $o .= ' '.trans('string.at').' ' . date('H:i', $time);
        }

        return $o;
    }



    public function handleSEO(Request $request, $item): void
    {
        $validator = Validator::make($request->all(), [
          'seo_title'       => '',
          'seo_description' => '',
          'seo_keywords'    => '',
        ]);

        $seo_title       = $request->input('seo_title');
        $seo_description = $request->input('seo_description');
        $seo_keywords    = $request->input('seo_keywords');

        $item->seo_title = $seo_title;
        $item->seo_description = $seo_description;
        $item->seo_keywords = $seo_keywords;

        $item->save();
    }

    public static function handleUpload(Request $request, string $uploadHandle, string $uploadMode, $item, string $uploadClass): bool
    {
        $forceAjax = false;
        switch ($uploadMode) {
            case 'ajax':
                $forceAjax = true;
                $uploadedData = $request->getContent();
                if ($uploadedData) {
                    $fileName = str_random(20);
                    $filePath = public_path() . '/files/temp/' . $fileName;
                    if (file_put_contents($filePath, $uploadedData)) {
                        $file = new UploadedFile($filePath, $fileName, null, filesize($filePath), 0, false);

                        $validator = Validator::make([ $uploadHandle => $file ], [ $uploadHandle => 'image|mimes:jpeg,gif,png' ]);
                        if (!$validator->fails()) {
                            $uploadClass::processFileUpload($item, $file);
                        }

                        unlink($filePath);
                    }
                }
                break;
            case 'formData':
                $forceAjax = true;
                // fall-through
            default:
                if ($files = $request->file($uploadHandle)) {
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            $validator = Validator::make([ $uploadHandle => $file ], [ $uploadHandle => 'image|mimes:jpeg,gif,png' ]);
                            if (!$validator->fails()) {
                                $uploadClass::processFileUpload($item, $file);
                            }
                        }
                    }
                }
                break;
        }

        self::dropUploads($uploadClass, $request->input('delete'));

        if ($forceAjax || $request->ajax()) {
            return response()->json([
                'success' => 1,
                'html'    => $item->uploadsGalleryForHandle($uploadHandle),
            ]);
        } else {
            return true;
        }
    }

    public static function dropUploads(string $uploadClass, array $ids = null): void
    {
        if ($ids && count($ids)) {
            foreach ($ids as $id) {
                $uploadClass::find($id)->delete();
            }
        }
    }

    public static function newPos(string $class): int
    {
        if ($item = $class::orderByRaw('pos desc')->first()) {
            return $item->pos + 1;
        } else {
            return 1;
        }
    }

    public static function newGrp(string $class): int
    {
        if ($item = $class::orderByRaw('grp desc')->first()) {
            return $item->grp + 1;
        } else {
            return 1;
        }
    }

    public static function unifySlug(string $class, string $slug, int $id = 0, array $exclude = []): string
    {
        if (!$slug) {
            $slug = str_random();
        }
        $original = $slug;
        $counter = 1;

        while ($class::where('slug', $slug)->where('id', '<>', $id)->first() || in_array($slug, $exclude)) {
            $slug = $original . $counter++;
        }

        return $slug;
    }

    public static function parseDefaultLanguage(string $http_accept = null, string $deflang = "en"): string
    {
        if ($http_accept && strlen($http_accept) > 1) {
            $x = explode(",", $http_accept);
            foreach ($x as $val) {
                if (preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i", $val, $matches)) {
                    $lang[$matches[1]] = (float)$matches[2];
                } else {
                    $lang[$val] = 1.0;
                }
            }

            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float)$value;
                    $deflang = $key;
                }
            }
        }
        return strtolower($deflang);
    }

    public static function getDefaultLanguage(): string
    {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            return self::parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        } else {
            return self::parseDefaultLanguage(null);
        }
    }

    public static function setLocale(): string
    {
        // $defaultLocale = ( strpos(self::getDefaultLanguage(),'ru') !== false ) ? 'ru' : 'en';

        // $force_https = getenv('FORCE_HTTPS');
        // $host = ( isset($_SERVER) && isset($_SERVER['HTTP_HOST']) ) ? $_SERVER['HTTP_HOST'] : false;

        // if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
        //     // SSL connection
        // }else if( $force_https ){
        //     switch( $host ){
        //         case 'www.nhmobile.com':
        //         case 'nhmobile.com':
        //             return Redirect::to("https://nhmobile.com" . $_SERVER['REQUEST_URI'], 301);
        //             break;
        //         case 'www.nhmobile.ru':
        //         case 'nhmobile.ru':
        //             return Redirect::to("https://nhmobile.ru" . $_SERVER['REQUEST_URI'], 301);
        //             break;
        //     }
        // }

        
        // switch( $host )
        // {
        //   case 'www.nhmobile.com':
        //     return Redirect::to("http" . ( $force_https ? 's' : '' ) . "://nhmobile.com" . $_SERVER['REQUEST_URI'], 301);
        //     break;
        //   case 'www.nhmobile.ru':
        //     return Redirect::to("http" . ( $force_https ? 's' : '' ) . "://nhmobile.ru" . $_SERVER['REQUEST_URI'], 301);
        //     break;
        //   case 'nh-ru.psytelepat.ru':
        //   case 'nhmobile.ru':
        //   case 'nh-ru':
        //     $locale = 'ru';
        //     break;
        //   case 'nh-en.psytelepat.ru':
        //   case 'nhmobile.com':
        //   case 'nh-en':
        //   default:
        //     $locale = 'en';
        //     break;
        // }

        $locale = 'ru';
        app()->setLocale($locale);
        return $locale;

        // $locales = ['ru','en'];

        // if( in_array($locale, $locales) ){
        //     app()->setLocale($locale);
        //     return $locale;
        // }else if( $locale == 'adm' || $locale == 'login' || $locale == 'logout' ){
        //     app()->setLocale($defaultLocale);
        //     return '';
        // }else{
        //     $segments = Request::segments();
        //     $segments[0] = $defaultLocale;
        //     redirect(implode('/', $segments))->send();
        // }
    }

    public static function sitemap(): void
    {
        $sitemap = App::make("sitemap");

        $pages = \App\SitePage::where('lng', Util::localeid())->where('dsp', '>', 0)->orderBy('updated_at', 'asc')->get();
        foreach ($pages as $page) {
            $sitemap->add(URL::to($page->pth), $page->updated_at, $page->priority, $page->freq);
        }

        $posts = \App\Blog\BlogPost::where('lng', Util::localeid())->where('dsp', '>', 0)->orderBy('updated_at', 'asc')->get();
        foreach ($posts as $post) {
            $sitemap->add(URL::to($post->url('view')), $post->updated_at, 0.6, 'weekly');
        }

        $sitemap->store('xml', 'sitemap-' . Lang::locale());
    }
}
