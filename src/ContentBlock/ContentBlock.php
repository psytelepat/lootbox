<?php

namespace Psytelepat\Lootbox\ContentBlock;

use Illuminate\Contracts\Support\Renderable;
use Psytelepat\Lootbox\ContentBlock\ContentBlockModel;
use Psytelepat\Lootbox\ContentBlock\ContentBlockImage;

use Illuminate\Support\Arr;

class ContentBlock
{

    /**
     * Загрузает запрашиваемый конфиг контентных блоков из config/content-block.php
     *
     * @param  string $cfg
     * @return object
     */
    public static function cfg(string $cfg) : object
    {
        $config = config('content-block.cfg.' . $cfg, []);
        $config['trg'] = array_get($config, 'trg', 1);
        $config['block_table'] = array_get($config, 'block_table', 'content_block');
        $config['block_modes'] = array_get($config, 'block_modes', [
            1 => 'text',
            2 => 'photo',
            3 => 'gallery',
            4 => 'video',
            5 => 'quote',
            6 => 'subscription',
            7 => 'double-column',
            8 => 'publicator-form',
        ]);
        $config['object_class'] = array_get($config, 'object_class');
        $config['block_class'] = array_get($config, 'block_class', ContentBlockModel::class);
        $config['image_class'] = array_get($config, 'image_class', ContentBlockImage::class);

        return (object)$config;
    }
    
    /**
     * Загружает конфиг контентных блоков из config/content-block.php
     * и подготавливает его для вывода в код страницы для JS
     *
     * @param  string $cfg
     * @return object
     */
    public static function jscfg(string $cfg) : object
    {
        $config = self::cfg($cfg);
        foreach ($config->block_modes as $key => $val) {
            $config->block_modes[$key] = [
                'tid' => $val,
                'name' => __('lootbox::content-block.block_mode_name.'.$key),
                'icon' => __('lootbox::content-block.block_mode_icon.'.$key),
            ];
        }
        return $config;
    }
    
    /**
     * Возвращает базовый URL для работы с контентными блоками
     *
     * @return string
     */
    public static function url() : string
    {
        return route(config('content-block.route.alias') . 'base');
    }
    
    /**
     * Возвращает инкрементируемое число при каждом вызове,
     * для назначения уникальных ID блокам на странице.
     * При вызове с false, возвращает предыдущее число.
     *
     * @param  bool $repeat
     * @return int
     */
    public static function uni(bool $repeat = false) : int
    {
        static $uni = 1;
        return $repeat ? ( $uni - 1 ) : $uni++;
    }
    
    /**
     * Возвращает следующую свободную позицию для блока для указанных $trg+$grp
     *
     * @param  int $trg
     * @param  int $usr
     * @param  string $class
     * @return int
     */
    public static function newPos(int $trg, int $usr, string $class = ContentBlockModel::class) : int
    {
        if ($item = $class::select('pos')->where('usr', $usr)->where('trg', $trg)->orderByRaw('pos desc')->first()) {
            return $item->pos + 1;
        } else {
            return 1;
        }
    }
    
    /**
     * Возвращает следующую свободную группу для блока для указанных $trg+$grp
     *
     * @param  int $trg
     * @param  int $usr
     * @param  string $class
     * @return int
     */
    public static function newGrp(int $trg, int $usr, string $class = ContentBlockModel::class) : int
    {
        if ($item = $class::select('grp')->where('usr', $usr)->where('trg', $trg)->orderByRaw('grp desc')->first()) {
            return $item->grp + 1;
        } else {
            return 1;
        }
    }
    
    /**
     * Вьюха с редактором блоков для указанных $trg+$grp
     *
     * @param  int $trg
     * @param  int $usr
     * @return Renderable
     */
    public static function embed(int $trg, int $usr) : Renderable
    {
        return view(config('content-block.views.prefix').'editor', [ 'trg' => $trg, 'usr' => $usr, ]);
    }

    /**
     * Вьюха с представлением блоков для указанных $trg+$grp
     *
     * @param  int $trg
     * @param  int $usr
     * @param array $vars опциональный массив с перменными для отрисовки блоков
     * @return Renderable
     */
    public static function htmlContentFor(int $trg, int $usr, array $vars = null) : string
    {
        $config = ContentBlock::cfg($trg);

        $html = '';
        if ($blocks = $config->block_class::where('trg', $trg)->where('usr', $usr)->orderByRaw('pos asc')->get()) {
            foreach ($blocks as $block) {
                $html .= $block->blockView($config, $vars);
            }
        }

        return $html;
    }
    
    /**
     * Дропнуть все блоки для указанных $trg+$grp
     *
     * @param  int $trg
     * @param  int $usr
     * @return int
     */
    public static function dropContentFor(int $trg, int $usr) : int
    {
        $config = ContentBlock::cfg($trg);

        $blocksDropped = 0;
        if ($blocks = $config->block_class::where('trg', $trg)->where('usr', $usr)->get()) {
            foreach ($blocks as $block) {
                $block->delete();
                $blocksDropped++;
            }
        }

        return $blocksDropped;
    }
    
    /**
     * Галерея загруженных файлов указанного класса для указанной модели
     *
     * @param  string $target
     * @param  ContentBlockModel $item
     * @return string
     */
    public static function uploadsGalleryForTarget(string $target, ContentBlockModel &$item) : string
    {
        if (!($config = config('lootbox.uploads.' . $target))) {
            throw new \Exception('Invalid upload handle');
        }

        $handle = Arr::get($config, 'handle');
        $object_class = Arr::get($config, 'object');
        $upload_class = Arr::get($config, 'upload');

        if ($item instanceof $object_class) {
            throw new \Exception('Invalid $item class');
        }

        return self::uploadsGallery($upload_class, $item, $target);
    }
    
    /**
     * Вьюха поля загрузки файла
     *
     * @param  string $target
     * @param  ContentBlockModel $item
     * @return string
     */
    public static function uploadField(string $target, ContentBlockModel $item = null) : string
    {
        if (!($config = config('lootbox.uploads.' . $target))) {
            throw new \Exception('Invalid upload handle');
        }

        $handle = Arr::get($config, 'handle');
        $object_class = Arr::get($config, 'object');
        $upload_class = Arr::get($config, 'upload');
        $multiple = Arr::get($config, 'multiple', true);

        return view('lootbox::content-block.form.image-file-upload', [
            'images'    => $item ? self::uploadsForGallery($upload_class, $item) : null,
            'handle'    => $handle.'[]',
            'multiple'  => $multiple,
            'uploadURL' => route('lootbox.content-block.upload.base', [
                'target' => $target,
                'usr' => $item ? $item->usr : null,
                'grp' => $item ? $item->grp : null,
                'trg' => $item ? $item->trg : null
            ]),
        ])->render();
    }
    
    /**
     * Количество загруженных файлов к объекту
     *
     * @param  string $upload_class класс загружаемых файлов
     * @param  ContentBlockModel $item объект модели
     * @return int
     */
    public static function uploadsCountForGallery(string $upload_class, ContentBlockModel &$item) : int
    {
        return $upload_class::where('usr', $item->id)->where('size', 1)->count();
    }

    /**
     * Загруженные файлы указанного класса для указанной модели
     *
     * @param  string $upload_class класс загружаемых файлов
     * @param  ContentBlockModel $item объект модели
     * @return Collection
     */
    public static function uploadsForGallery(string $upload_class, ContentBlockModel &$item)
    {
        return $upload_class::where('usr', $item->id)->where('size', $upload_class::getPreviewSize())->orderBy('pos', 'asc')->get();
    }
    
    /**
     * Галерея загруженных файлов указанного класса для указанной модели
     *
     * @param  string $upload_class
     * @param  ContentBlockModel $item
     * @param  string $target
     * @return string
     */
    public static function uploadsGallery(string $upload_class, ContentBlockModel &$item, string $target) : string
    {
        $view = view('lootbox::content-block.form.image-file-upload-gallery', [
            'images'    => self::uploadsForGallery($upload_class, $item),
            'uploadURL' => route('lootbox.content-block.upload.base', [
                'target' => $target,
                'usr' => $item ? $item->usr : null,
                'grp' => $item ? $item->grp : null,
                'trg' => $item ? $item->trg : null
            ]),
        ])->render();

        return $view;
    }
}
