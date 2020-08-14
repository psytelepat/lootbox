<?php

namespace Psytelepat\Lootbox\Http\Controllers\ContentBlock;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Arr;

use Image;
use Validator;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\ContentBlock\ContentBlock;
use Psytelepat\Lootbox\ContentBlock\ContentBlockModel;
use Psytelepat\Lootbox\ContentBlock\ContentBlockImage;

class ContentBlockUploadController extends Controller
{
    protected $cfg;

    protected $target;
    protected $config;

    protected $handle;
    protected $object_class;
    protected $upload_class;
    protected $limit;
    protected $upload_type = 'image';

    protected function resolveTarget(string $target): void
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

    protected function validateUploadedFile(&$file): bool
    {
        $validator = Validator::make([ $this->handle => $file ], [ $this->handle => 'image|mimes:jpeg,gif,png' ]);
        return !$validator->fails();
    }

    public function upload(Request $request, int $trg, int $usr, int $grp, string $target, string $uploadMode = 'form')
    {
        $this->cfg = ContentBlock::cfg($trg);
        $this->resolveTarget($target);

        if (!( $item = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $grp)->first() )) {
            return response()->json([ 'error' => 1, 'message' => 'Unable to load block' ], 404);
        }

        $uploadsCount = ContentBlock::uploadsCountForGallery($this->upload_class, $item);
        if ($this->limit > 0 && $uploadsCount >= $this->limit) {
            return response()->json([ 'error' => 1, 'message' => 'Images limit exceed', ]);
        }

        $limit = $this->limit > 0 ? $this->limit - $uploadsCount : PHP_INT_MAX;

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
                            $this->upload_class::processFileUpload($item, $file, 'jpg');
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
                                $this->upload_class::processFileUpload($item, $file, 'jpg');
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
            return [
                'success' => 1,
                'html'    => ContentBlock::uploadsGallery($this->upload_class, $item, $target),
                'canUpload' => $limit > 0,
            ];
        } else {
            return true;
        }
    }

    public function repos(Request $request, int $trg, int $usr, int $grp, string $target, int $from_id, int $to_id): array
    {
        $this->cfg = ContentBlock::cfg($trg);
        $this->resolveTarget($target);

        if (!( $item = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $trg)->first() )) {
            return response('Error 404', 404);
        }

        $this->upload_class::repos($from_id, $to_id);

        return [
            'success' => 1,
            'html' => ContentBlock::uploadsGallery($this->upload_class, $item, $this->target),
        ];
    }

    public function edit(Request $request, int $trg, int $usr, int $grp, string $target, int $fileID): array
    {
        $this->cfg = ContentBlock::cfg($trg);
        $this->resolveTarget($target);

        if (!( $item = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $trg)->first() )) {
            return response()->json([ 'error' => 1, ]);
        }
        
        if (! ( $file = $this->cfg->image_class::find($fileID) )) {
            return response()->json([ 'error' => 1, ]);
        }

        if ($request->isMethod('post')) {
            $file->updateSEO([
                'alt'           => $request->input('alt'),
                'title'         => $request->input('title'),
                'description'   => $request->input('description'),
                'href'          => $request->input('href'),
            ]);

            return [
                'success' => 1,
                'file' => $file,
            ];
        } else {
            return [
                'success' => 1,
                'html' => view('lootbox::content-block.form.image-file-editor', [
                    'form_path' => route('lootbox.content-block.upload.edit', [
                        'target' => $this->target,
                        'trg' => $item->trg,
                        'usr' => $item->usr,
                        'grp' => $item->grp,
                        'fileID' => $file->id
                    ]),
                    'file'      => $file,
                ])->render()
            ];
        }
    }

    public function delete(Request $request, int $trg, int $usr, int $grp, string $target): array
    {
        $this->cfg = ContentBlock::cfg($trg);
        $this->resolveTarget($target);

        if (!( $item = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $grp)->first() )) {
            return response()->json([ 'success' => 0, 'error' => 1 ]);
        }

        $ids = explode(',', $request->input('delete'));
        if ($ids && is_array($ids) && count($ids)) {
            foreach ($ids as $id) {
                $this->upload_class::find($id)->delete();
            }
        }

        return [
            'success' => 1,
            'html' => ContentBlock::uploadsGallery($this->upload_class, $item, $this->target),
            'canUpload' => $this->limit > 0 && ContentBlock::uploadsCountForGallery($this->upload_class, $item) < $this->limit,
        ];
    }

    public function reset(): void
    {
        foreach (ContentBlockModel::all() as $item) {
            if ($randomImage = $item->randomImage()) {
                $randomImage->resetPositions();
            }
        }
        echo 'Positions resetted.';
        exit;
    }

    public function crop(Request $request, $trg, $usr, $grp, $target, $fileID)
    {
        $this->cfg = ContentBlock::cfg($trg);
        $this->resolveTarget($target);

        if (!( $item = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $trg)->first() )) {
            return response()->json([ 'error' => 1, ]);
        }
        
        if (! ( $file = $this->cfg->image_class::find($fileID) )) {
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

        $sizes = config('lootbox.images.' . trim($this->cfg->image_class, '\\'));
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
