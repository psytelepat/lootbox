<?php

namespace Psytelepat\Lootbox\User;

use Illuminate\Database\Eloquent\Model;
use Psytelepat\Lootbox\User\Role;

class Permission extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    public $table = 'permissions';
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Role::class)->distinct();
    }

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::common.permissions_new');
            break;
            case 'delete':
                return __('lootbox::common.permissions_delete', [ 'title' => $model->key ]);
            break;
            default:
                return $model->key;
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null): string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.permissions.create');
            break;
            case 'edit':
                return route('lootbox.permissions.edit', $model);
            break;
            case 'delete':
                return route('lootbox.permissions.delete', $model);
            break;
            case 'index':
                return route('lootbox.permissions.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
