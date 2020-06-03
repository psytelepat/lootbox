<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use App\Http\Controllers\Controller;
use Psytelepat\Lootbox\View\DefaultTemplate;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Psytelepat\Lootbox\Util;

abstract class AbstractController extends Controller
{
    protected $template;

    public function __construct()
    {
        $this->template = new DefaultTemplate();
    }
}
