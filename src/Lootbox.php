<?php

namespace Psytelepat\Lootbox;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Lootbox
{
    public static function uploadsGalleryForTarget(string $target, Model &$item): string
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

    public static function uploadField(string $target, Model $item = null): string
    {
        if (!($config = config('lootbox.uploads.' . $target))) {
            throw new \Exception('Invalid upload handle');
        }

        $handle = Arr::get($config, 'handle');
        $object_class = Arr::get($config, 'object');
        $upload_class = Arr::get($config, 'upload');
        $multiple = Arr::get($config, 'multiple', true);
        $limit = intval(Arr::get($config, 'limit', 0));
        $images = $item ? self::uploadsForGallery($upload_class, $item): null;

        return view('lootbox::content-block.form.image-file-upload', [
            'images'    => $images,
            'handle'    => $handle.'[]',
            'multiple'  => $multiple,
            'canUpload' => $limit <= 0 || ($images && count($images) < $limit),
            'uploadURL' => route('lootbox.upload.base', [ 'target' => $target, 'id' => $item ? $item->id : null ]),
        ])->render();
    }

    public static function uploadsForGallery(string $upload_class, Model &$item): Collection
    {
        return $upload_class::where('usr', $item->id)
            ->where('size', $upload_class::getPreviewSize())
            ->orderBy('pos', 'asc')
            ->get();
    }

    public static function uploadsGallery(string $upload_class, Model &$item, string $target): string
    {
        if (!($config = config('lootbox.uploads.' . $target))) {
            throw new \Exception('Invalid upload handle');
        }

        $limit = intval(Arr::get($config, 'limit', 0));
        $images = self::uploadsForGallery($upload_class, $item);

        $view = view('lootbox::content-block.form.image-file-upload-gallery', [
            'images'    => $images,
            'canUpload' => $limit <= 0 || count($images) < $limit,
            'uploadURL' => route('lootbox.upload.base', [ 'target' => $target, 'id' => $item ? $item->id : null ]),
        ])->render();

        return $view;
    }
}
