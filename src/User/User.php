<?php

namespace Psytelepat\Lootbox\User;

use Carbon\Carbon;
use Psytelepat\Lootbox\User\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Psytelepat\Lootbox\User\Avatar;

class User extends Authenticatable implements \Psytelepat\Lootbox\AdminableModelInterface
{
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function roles_all()
    {
        $this->loadRolesRelations();

        return collect([$this->role])->merge($this->roles);
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role(s) to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        $roles = $this->roles_all()->pluck('name')->toArray();

        foreach ((is_array($name) ? $name : [$name]) as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set default User Role.
     *
     * @param string $name The role name to associate.
     */
    public function setRole($name)
    {
        $role = Role::where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        $this->loadPermissionsRelations();

        $_permissions = $this->roles_all()
                              ->pluck('permissions')->flatten()
                              ->pluck('key')->unique()->toArray();

        return in_array($name, $_permissions);
    }

    public function hasPermissionOrFail($name)
    {
        if (!$this->hasPermission($name)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function hasPermissionOrAbort($name, $statusCode = 403)
    {
        if (!$this->hasPermission($name)) {
            return abort($statusCode);
        }

        return true;
    }

    private function loadRolesRelations()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
    }

    private function loadPermissionsRelations()
    {
        $this->loadRolesRelations();

        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
            $this->load('roles.permissions');
        }
    }

    public function avatarFieldView()
    {
        return view(config('content-block.views.prefix').'form.image-file-upload', [
            'handle'    => 'avatar',
            'multiple'  => false,
            'images'    => $this->avatars(2),
            'uploadURL' => route('lootbox.profile.avatar.base'),
        ])->render();
    }

    public function avatars($size = 1)
    {
        return Avatar::where('usr', $this->id)->where('size', $size)->orderBy('pos', 'asc')->get();
    }

    public function uploadsGalleryForHandle($handle)
    {
        switch ($handle) {
            case 'avatar':
                return view(config('content-block.views.prefix').'form.image-file-upload-gallery', [
                    'images'    => $this->avatars(2),
                    'uploadURL' => route('lootbox.profile.avatar.base'),
                ])->render();
                break;
        }
    }

    public function dropAvatars($ids)
    {
        if ($ids && is_array($ids) && count($ids)) {
            foreach ($ids as $id) {
                Avatar::find($id)->delete();
            }
        }
    }

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::common.users_new');
            break;
            case 'delete':
                return __('lootbox::common.users_delete', [ 'email' => $model->email, 'name' => $model->name ]);
            break;
            default:
                return $model->email;
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null): string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.users.create');
            break;
            case 'edit':
                return route('lootbox.users.edit', $model);
            break;
            case 'delete':
                return route('lootbox.users.delete', $model);
            break;
            case 'index':
                return route('lootbox.users.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
