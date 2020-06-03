<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use Hash;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;

class LootboxController extends BaseController
{
    public function index(Request $request)
    {
        $this->template->content[] = view('lootbox::admin.dashboard');
        return $this->template;
    }
}
