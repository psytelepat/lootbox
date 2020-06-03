<?php

namespace Psytelepat\Lootbox\User;

use Illuminate\Database\Eloquent\Model;
use Psytelepat\Lootbox\User\User;
use Psytelepat\Lootbox\User\Permission;

class Role extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    public $table = 'roles';

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::common.roles_new');
            break;
            case 'delete':
                return __('lootbox::common.roles_delete', [ 'title' => $model->display_name ]);
            break;
            default:
                return $model->display_name;
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null): string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.roles.create');
            break;
            case 'edit':
                return route('lootbox.roles.edit', $model);
            break;
            case 'delete':
                return route('lootbox.roles.delete', $model);
            break;
            case 'index':
                return route('lootbox.roles.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
