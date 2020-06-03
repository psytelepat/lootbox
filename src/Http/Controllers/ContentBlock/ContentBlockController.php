<?php

namespace Psytelepat\Lootbox\Http\Controllers\ContentBlock;

use Illuminate\Http\Request;

use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;

use App;
use Validator;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\ContentBlock\ContentBlock;
use Psytelepat\Lootbox\ContentBlock\ContentBlockModel;

/**
 * Контроллер контентных блоков
 */
class ContentBlockController extends BaseController
{
    protected $cfg;

    protected function loadConfig(int $trg)
    {
        $this->cfg = ContentBlock::cfg($trg);
    }

    protected function loadBlock(int $trg, int $usr, int $grp, int $lng = null): ?ContentBlockModel
    {
        $query = $this->cfg->block_class::where('trg', $trg)->where('usr', $usr)->where('grp', $grp);
        if ($lng) {
            $query->where('lng', $lng ? $lng : Util::localeid());
        }
        return $query->first();
    }

    public function index(Request $request, int $trg, int $usr)
    {
        $this->template->content[] = view($this->views_prefix.'editor', [ 'trg' => $trg, 'usr' => $usr]);
        return $this->template;
    }

    public function create(Request $request, int $trg, int $usr, int $mode): array
    {
        $this->loadConfig($trg);

        if (!in_array($mode, array_keys($this->cfg->block_modes))) {
            return response()->json([ 'error' => 1, 'message' => 'invalid mode' ], 406);
        }

        $block = new $this->cfg->block_class;
        $block->trg = $trg;
        $block->usr = (int)$usr;
        $block->lng = Util::localeid();
        
        if ($this->cfg->object_class && ( $object = $this->cfg->object_class::find($usr) ) && $object->lng) {
            $block->lng = $object->lng;
        }
        
        $block->grp = ContentBlock::newGrp($this->cfg->trg, $usr);
        $block->pos = ContentBlock::newPos($this->cfg->trg, $usr);
        $block->mode = $mode;

        switch ($block->mode) {
            case 7:
                $block->content = json_encode(['content1' => '','content2' => '',], JSON_UNESCAPED_UNICODE);
                break;
        }

        $block->save();

        return [
            'success'   => 1,
            'grp'       => $block->grp,
            'mode'      => $block->mode,
            'html'      => $block->blockForm($this->cfg),
        ];
    }

    public function view(Request $request, int $trg, int $usr, int $grp): array
    {
        $this->loadConfig($trg);

        if (!($block = $this->loadBlock($trg, $usr, $grp))) {
            return response()->json([ 'error' => 1, 'message' => 'Unable to find requested block' ], 404);
        }

        return [
            'success' => 1,
            'html'    => $block->blockView($this->cfg),
        ];
    }

    public function edit(Request $request, int $trg, int $usr, int $grp): array
    {
        $this->loadConfig($trg);
        if (!($block = $this->loadBlock($trg, $usr, $grp))) {
            return response()->json([ 'error' => 1, 'message' => 'Unable to find requested block' ], 404);
        }

        if ($request->isMethod('post')) {
            switch ($block->mode) {
                case 1:
                case 2:
                    $content = $request->input('content');
                    $block->content = $content;
                    break;
                case 3:
                    break;
                case 4:
                    $code = $request->input('code');
                    $block->code = $code;
                    break;
                case 5:
                    $title = $request->input('title');
                    $block->title = $title;

                    $description = $request->input('description');
                    $block->description = $description;

                    $content = $request->input('content');
                    $block->content = $content;
                    break;
                case 7:
                    $content1 = $request->input('content1');
                    $content2 = $request->input('content2');
                    $block->content = json_encode([
                        'content1' => $content1,
                        'content2' => $content2,
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                case 8:
                    $title = $request->input('title');
                    $description = $request->input('description');
                    $content = $request->input('content');

                    $block->title = $title;
                    $block->description = $description;

                    $block->content = json_encode([
                        'mode' => $request->input('mode'),
                    ], JSON_UNESCAPED_UNICODE);
                    break;
            }

            $block->save();

            return [
                'success' => 1,
                'html'    => $block->blockView($this->cfg),
            ];
        } else {
            return [
                'success' => 1,
                'html'    => $block->blockForm($this->cfg),
            ];
        }
    }

    public function repos(Request $request, int $trg, int $usr, int $grp, int $to): array
    {
        $this->loadConfig($trg);

        if (!($block_from = $this->loadBlock($trg, $usr, $grp))) {
            return response()->json([ 'error' => 'unable to find from block' ], 404);
        }
        
        if (!($block_to=$this->loadBlock($trg, $usr, $to))) {
            return response()->json([ 'error' => 'unable to find to block' ], 404);
        }

        $block_from->reposTo($block_to);

        return [ 'success' => 1 ];
    }

    public function delete(Request $request, int $trg, int $usr, int $grp): array
    {
        $this->loadConfig($trg);

        if (!($block = $this->loadBlock($trg, $usr, $grp))) {
            return response()->json([ 'error' => 1, 'message' => 'Unable to find requested block' ], 404);
        }

        $block->delete();

        return [ 'success' => 1 ];
    }
}
