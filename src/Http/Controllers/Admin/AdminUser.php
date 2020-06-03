<?php

namespace Psytelepat\Lootbox\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

use Hash;

/**
 * Контроллер юзеров
 */
class AdminUser extends AdminController
{
    protected $MODEL_CLASS = 'App\User';
    protected $FORM = 'lootbox::users.form';
    protected $ADMIN_ROUTE = 'users';
    protected $LANG_ROUTE = 'lootbox::common.users';

    public static function validationRules(string $mode, Model $model = null): array
    {
        return [
            'name' => 'required',
            'email' => ( $mode === 'create' ) ? 'required|email|unique:users,email,'.( $model ? $model->id : null ) : 'unique:users,email,'.( $model ? $model->id : null ),
            'role_id' => 'required|exists:roles,id',
            'password' => ( $mode === 'create' ) ? 'required' : 'nullable',
            'password_confirm' => ( $mode === 'create' ) ? 'required' : 'nullable',
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('email')->sortable()->searchable()->link($link);
        $list->column('name')->sortable()->searchable()->link($link);
        $list->column('created_at')->sortable();
        $list->column('updated_at')->sortable();
    }

    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        $model->name = $request->input('name');
        $model->role_id = $request->input('role_id');

        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');
        if ($password && $password_confirm && ( $password === $password_confirm )) {
            $model->password = Hash::make($password);
        }

        switch ($mode) {
            case 'create':
                $model->email = $request->input('email');
                break;
            case 'edit':
                break;
            default:
                throw new \InvalidArgumentException('Invalid $mode');
            break;
        }

        return true;
    }
}
