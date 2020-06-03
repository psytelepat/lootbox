<?php

namespace Psytelepat\Lootbox\View;

use Psytelepat\Lootbox\View\Template;
use Auth;
use Route;

use Illuminate\Support\Arr;

class Navbar extends Template
{
    protected $layout = 'lootbox::admin.navbar';

    protected $menu;
    protected $current_route;
    protected $current_section;
    protected $current_route_params;

    public function __construct(string $layout = null, array $data = [], string $config = 'lootbox.menu.navbar')
    {
        parent::__construct($layout, $data);

        $this->menu = config($config);
        $this->bind('menu', $this->menu);
        $this->current_route = Route::currentRouteName();
        $this->current_section = Arr::get(Route::current()->action, 'section');
        $this->current_route_params = Route::current()->parameters();

        $this->processMenuItems($this->menu);
    }

    public static function factory(string $layout = null, array $data = [], string $config = 'lootbox.menu.navbar'): Template
    {
        return new static($layout, $data, $config);
    }

    protected function getRouteGroup(string $route): string
    {
        $group = explode('.', $route);
        array_pop($group);
        return implode('.', $group);
    }

    protected function compareRoutes(string $route1, string $route2): bool
    {
        return $this->getRouteGroup($route1) == $this->getRouteGroup($route2);
    }

    protected function compareRoutesParams(array $p1, array $p2): bool
    {
        return $p1 == $p2;
    }

    protected function processMenuItems(&$items): bool
    {
        $has_active_item = false;
        foreach ($items as $key => $item) {
            $active = false;
            if (array_has($item, 'route')) {
                $route = Arr::get($item, 'route');
                $section = Arr::get($item, 'section');
                $route_params = Arr::get($item, 'route_params', []);
                $item['url'] = (string)route($route, $route_params);
                $item['title'] = __($item['title']);
                if ($active = ( $this->current_section && ( $this->current_section === $section ) && ( empty($route_params) || $this->compareRoutesParams($route_params, $this->current_route_params) ) )) {
                    $has_active_item = true;
                }
            }

            if ($item['has_items'] = Arr::has($item, 'items')) {
                if ($this->processMenuItems($item['items'])) {
                    $active = true;
                }
            }

            $item['active'] = $active;
            $items[$key] = $item;
        }

        return $has_active_item;
    }

    public function render(): string
    {
        if (Auth::user()) {
            $this->set('user_id', auth()->user()->id);
            $this->set('user_name', auth()->user()->name);
        }

        return parent::render();
    }
}
