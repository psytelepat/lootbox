<?php

namespace Psytelepat\Lootbox\Publicator;

use Psytelepat\Lootbox\Util;
use Illuminate\Database\Eloquent\Model;

class PublicatorSubscription extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    protected $table = 'publicator_subscription';
    protected $fillable = [
        'email',
        'payload',
    ];

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::publicator.subscriptions_new');
            break;
            case 'delete':
                return __('lootbox::publicator.subscriptions_delete', [ 'title' => $model->email ]);
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
                return route('lootbox.publicator.subscription.create');
            break;
            case 'edit':
                return route('lootbox.publicator.subscription.edit', $model);
            break;
            case 'delete':
                return route('lootbox.publicator.subscription.delete', $model);
            break;
            case 'index':
                return route('lootbox.publicator.subscription.index');
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
