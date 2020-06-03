<?php

namespace Psytelepat\Lootbox;

use Illuminate\Database\Eloquent\Model;

interface AdminableModelInterface
{
    public static function adminFormTitle(string $mode = null, Model $model = null): string;
    public static function adminRoute(string $mode = 'index', Model $model = null): string;
}
