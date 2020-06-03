<?php

namespace Psytelepat\Lootbox;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Psytelepat\Lootbox\Http\Middleware\LootboxAdminMiddleware;
use Psytelepat\Lootbox\Http\Middleware\LootboxAfterMiddleware;

use Psytelepat\Lootbox\Lootbox;
use Psytelepat\Lootbox\Publicator\Publicator;
use Psytelepat\Lootbox\ContentBlock\ContentBlock;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../publishable/config/lootbox.php', 'lootbox');
        $this->mergeConfigFrom(__DIR__.'/../publishable/config/publicator.php', 'publicator');
        $this->mergeConfigFrom(__DIR__.'/../publishable/config/content-block.php', 'content-block');
        $this->mergeConfigFrom(__DIR__.'/../publishable/config/site-settings.php', 'site-settings');

        $this->app->singleton(Lootbox::class, function () {
            return new Lootbox();
        });
        $this->app->alias(Lootbox::class, 'Lootbox');

        $this->app->singleton(ContentBlock::class, function () {
            return new ContentBlock();
        });
        $this->app->alias(ContentBlock::class, 'ContentBlock');

        $this->app->singleton(Publicator::class, function () {
            return new Publicator();
        });
        $this->app->alias(Publicator::class, 'Publicator');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(Router $router, Dispatcher $event): void
    {
        \Psytelepat\Lootbox\Util::init();

        $this->loadRoutesFrom(__DIR__.'/../publishable/routes/lootbox.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'lootbox');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'lootbox');

        $this->publishes([
            __DIR__.'/../publishable/config/lootbox.php' => config_path('lootbox.php'),
            __DIR__.'/../publishable/config/content-block.php' => config_path('content-block.php'),
            __DIR__.'/../publishable/config/publicator.php' => config_path('publicator.php'),
            __DIR__.'/../publishable/config/site-settings.php' => config_path('site-settings.php'),
            __DIR__.'/../publishable/assets' => public_path('assets'),
        ]);

        $router->aliasMiddleware('admin.user', LootboxAdminMiddleware::class);
        $router->aliasMiddleware('admin.after', LootboxAfterMiddleware::class);

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
