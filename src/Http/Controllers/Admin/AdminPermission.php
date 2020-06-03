<?php

namespace Psytelepat\Lootbox\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

/**
 * Контроллер пермишенов
 */
class AdminPermission extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\User\Permission';
    protected $FORM = 'lootbox::users.permissions.form';
    protected $ADMIN_ROUTE = 'permissions';
    protected $LANG_PREFIX = 'lootbox::common.permissions';

    public static function validationRules(string $mode, Model $model = null): array
    {
        return [
            'group' => 'required|regex:/^[A-Za-z0-9-_]+$/|max:255',
            'key' => 'required|regex:/^[A-Za-z0-9-_]+$/|unique:permissions,key,'.( $model ? $model->id : null ).'|max:255',
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('group')->sortable()->searchable();
        $list->column('key')->sortable()->searchable()->link($link);
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                $model->group = $request->input('group');
                $model->key = $request->input('key');
                break;
            default:
                throw new \InvalidArgumentException('Invalid $mode');
            break;
        }
    }
}
