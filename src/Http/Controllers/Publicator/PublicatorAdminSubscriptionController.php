<?php

namespace Psytelepat\Lootbox\Http\Controllers\Publicator;

use Illuminate\Http\Request;
use App\Http\Requests;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Publicator\PublicatorSubscription as Subscription;

/**
 * Контроллер подписок на публикации
 */
class PublicatorAdminSubscriptionController extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\Publicator\PublicatorSubscription';
    protected $FORM = 'lootbox::publicator.subscription-form';
    protected $ADMIN_ROUTE = 'publicator.subscription';
    protected $LANG_ROUTE = 'lootbox::publicator.subscriptions';

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable(true, 'asc')->link($link);
        $list->column('email')->sortable()->searchable()->link($link);
        $list->column('created_at');
    }
}
