<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use App\Http\Controllers\Controller;
use Psytelepat\Lootbox\View\DefaultTemplate;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Image;
use Lootbox;
use Validator;
use Psytelepat\Lootbox\Util;

use Illuminate\Http\UploadedFile;

class UploadController extends Controller
{
    protected $target;
    protected $config;

    protected $handle;
    protected $object_class;
    protected $upload_class;
    protected $limit;
    protected $upload_type = 'image';

    protected function resolveTarget(string $target)
    {
        $this->target = $target;
        $this->config = config('lootbox.uploads.' . $this->target);
        if (!$this->config) {
            throw new \Exception('Invalid upload handle');
        }

        $this->handle = Arr::get($this->config, 'handle');
        $this->object_class = Arr::get($this->config, 'object');
        $this->upload_class = Arr::get($this->config, 'upload');
        $this->limit = Arr::get($this->config, 'limit', 0);
    }

    public function view(Request $request, string $target, int $id = null)
    {
        $this->resolveTarget($target);

        $item = $id ? $this->object_class::find($id) : null;

        return Lootbox::uploadField($target, $item);
    }

    protected function validateUploadedFile(&$file)
    {
        $validator = Validator::make([ $this->handle => $file ], [ $this->handle => 'image|mimes:jpeg,gif,png' ]);
        return !$validator->fails();
    }

    public function upload(Request $request, string $target, int $id, string $uploadMode)
    {
        $this->resolveTarget($target);
        if (! ( $item = $this->object_class::find($id) )) {
            return response()->json([ 'error' => 1, 'message' => 'Unable to find object', ]);
        }

        if ($this->limit > 0 && Lootbox::uploadsCountForGallery($this->upload_class, $item) >= $this->limit) {
            return response()->json([ 'error' => 1, 'message' => 'Images limit exceed', ]);
        }

        $limit = $this->limit > 0 ? $this->limit : PHP_INT_MAX;

        $file = null;
        $forceAjax = null;
        $tempFilePath = null;
        switch ($uploadMode) {
            case 'ajax':
                $forceAjax = true;
                $uploadedData = $request->getContent();
                if ($uploadedData) {
                    $tempFilePath = public_path() . '/tmp/' . str_random(20);
                    if (file_put_contents($tempFilePath, $uploadedData)) {
                        $file = new UploadedFile($tempFilePath, $fileName, null, filesize($tempFilePath), 0, false);
                        if ($this->validateUploadedFile($file)) {
                            $this->upload_class::processFileUpload($item, $file);
                            $limit--;
                        }
                        unlink($tempFilePath);
                    }
                }
                break;
            case 'formData':
                $forceAjax = true;
                // fall-through
            default:
                if ($files = $request->file($this->handle)) {
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            if ($this->validateUploadedFile($file)) {
                                $this->upload_class::processFileUpload($item, $file);
                                $limit--;
                                if ($limit <= 0) {
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
        }

        Util::dropUploads($this->upload_class, $request->input('delete'));

        if ($forceAjax || $request->ajax()) {
            return response()->json([
                'success' => 1,
                'html'    => Lootbox::uploadsGallery($this->upload_class, $item, $target),
                'canUpload' => $limit > 0,
            ]);
        } else {
            return true;
        }
    }

    public function repos(Request $request, string $target, int $id, int $from, int $to)
    {
        $this->resolveTarget($target);
        if (! ( $item = $this->object_class::find($id) )) {
            return response()->json([ 'error' => 1, ]);
        }
        $this->upload_class::repos($from, $to);
        return response()->json([ 'success' => 1, 'html' => Lootbox::uploadsGallery($this->upload_class, $item, $this->target), ]);
    }

    public function delete(Request $request, string $target, int $id)
    {
        $this->resolveTarget($target);

        if (! ( $item = $this->object_class::find($id) )) {
            return response()->json([ 'error' => 1, ]);
        }

        $ids = explode(',', $request->input('delete'));
        if ($ids && is_array($ids) && count($ids)) {
            foreach ($ids as $id) {
                $this->upload_class::find($id)->delete();
            }
        }

        return [
            'success' => 1,
            'html' => Lootbox::uploadsGallery($this->upload_class, $item, $this->target),
            'canUpload' => $this->limit > 0 && Lootbox::uploadsCountForGallery($this->upload_class, $item) < $this->limit,
        ];
    }

    public function edit(Request $request, string $target, int $id, int $fileID)
    {
        $this->resolveTarget($target);

        if (!is_subclass_of($this->upload_class, '\Psytelepat\Lootbox\ContentBlock\BaseImage')) {
            return [
                'error' => 1,
                'message' => 'not an image',
            ];
        }

        if (! ( $item = $this->object_class::find($id) )) {
            return response()->json([ 'error' => 1, ]);
        }
        if (! ( $file = $this->upload_class::find($fileID) )) {
            return response()->json([ 'error' => 1, ]);
        }

        if ($request->isMethod('POST')) {
            $file->updateSEO([
                'alt'           => $request->input('alt'),
                'title'         => $request->input('title'),
                'description'   => $request->input('description'),
                'href'          => $request->input('href'),
            ]);

            return [ 'success' => 1, 'file' => $file, ];
        }

        return [
            'success' => 1,
            'html' => view(config('content-block.views.prefix').'form.image-file-editor', [
                'form_path' => route('lootbox.upload.edit', [ 'target' => $this->target, 'id' => $item->id, 'fileID' => $file->id ]),
                'file'      => $file,
            ])->render()
        ];
    }

    public function crop(Request $request, string $target, int $id, int $fileID)
    {
        $this->resolveTarget($target);

        if (!is_subclass_of($this->upload_class, '\Psytelepat\Lootbox\ContentBlock\BaseImage')) {
            return [
                'error' => 1,
                'message' => 'not an image',
            ];
        }

        if (! ( $item = $this->object_class::find($id) )) {
            return response()->json([ 'error' => 1, ]);
        }
        if (! ( $file = $this->upload_class::find($fileID) )) {
            return response()->json([ 'error' => 1, ]);
        }

        if ($request->isMethod('POST')) {
            $width = $request->input('width');
            $height = $request->input('height');
            $x = $request->input('x');
            $y = $request->input('y');

            foreach ($file->childs as $child) {
                $image = Image::make(storage_path('app/public/'.$file->fullPath()));
                $image->crop($width, $height, $x, $y);
                $child->replaceWithImage($image);
            }

            return [ 'success' => 1, 'file' => $file, ];
        }

        $sizes = config('lootbox.images.' . trim($this->upload_class, '\\'));
        $size = is_array($sizes) && !empty($sizes) ? Arr::get($sizes, 0) : null;

        if (empty($size) || !is_array($size) || !array_key_exists('crop', $size)) {
            return [ 'error' => 'not croppable', 'message' => 'not croppable' ];
        } else {
            $targetWidth = $targetHeight = $targetAspect = null;
            if (is_array($size['crop'])) {
                $targetWidth = Arr::get($size['crop'], 'width', null);
                $targetHeight = Arr::get($size['crop'], 'height', null);
                $targetAspect = Arr::get($size['crop'], 'aspect', null);
            }

            return [
                'success' => 1,
                'html' => view(config('content-block.views.prefix').'form.cropper', [
                    'form_path' => route('lootbox.upload.crop', [ 'target' => $this->target, 'id' => $item->id, 'fileID' => $file->id ]),
                    'file'      => $file,
                    'targetWidth' => $targetWidth,
                    'targetHeight' => $targetHeight,
                    'targetAspect' => $targetAspect,
                ])->render()
            ];
        }
    }
}
