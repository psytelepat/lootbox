<?php

namespace Psytelepat\Lootbox\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

/**
 * Контроллер ролей
 */
class AdminRole extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\User\Role';
    protected $FORM = 'lootbox::users.roles.form';
    protected $ADMIN_ROUTE = 'roles';
    protected $LANG_ROUTE = 'lootbox::common.roles';

    public static function validationRules(string $mode, Model $model = null): array
    {
        return [
            'name' => 'required|regex:/^[A-Za-z0-9-_]+$/|unique:roles,name,'.( $model ? $model->id : null ).'|max:255',
            'display_name' => 'required|unique:roles,display_name,'.( $model ? $model->id : null ).'|max:255',
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('name')->sortable()->searchable()->link($link);
        $list->column('display_name')->sortable()->searchable()->link($link);
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                $model->name = $request->input('name');
                $model->display_name = $request->input('display_name');
                break;
            default:
                throw new \InvalidArgumentException('Invalid $mode');
            break;
        }
    }
}
