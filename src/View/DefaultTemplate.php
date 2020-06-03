<?php

namespace Psytelepat\Lootbox\View;

use Psytelepat\Lootbox\View\Template;
use Psytelepat\Lootbox\View\Navbar;
use Psytelepat\Lootbox\View\Views;
use Psytelepat\Lootbox\View\Scripts;
use Psytelepat\Lootbox\View\Styles;

class DefaultTemplate extends Template
{
    protected $layout = 'lootbox::layout.default';
    protected $properties = [
        'styles',
        'scripts_in_header',
        'title',
        'navbar',
        'top_navbar',
        'header',
        'content',
        'footer',
        'modals',
        'scripts_in_footer',
    ];
    
    public $styles;
    public $scripts_in_header;
    public $title;
    public $navbar;
    public $top_navbar;
    public $header;
    public $content;
    public $footer;
    public $modals;
    public $scripts_in_footer;

    public function __construct(string $layout = null, array $data = [])
    {
        parent::__construct($layout, $data);

        $this->styles = Styles::factory([
            'assets/inspinia/css/bootstrap.min.css',
            'assets/inspinia/font-awesome/css/all.css',
            'assets/inspinia/css/animate.css',
            'assets/inspinia/css/style.css',

            'assets/lootbox/lootbox.css',
        ]);

        $this->scripts_in_header = Scripts::factory();

        $this->title = config('site-settings.site_title');

        $this->navbar = Navbar::factory();

        $this->top_navbar = view('lootbox::admin.top-navbar');

        $this->header = view('lootbox::admin.header');

        $this->content = Views::factory();

        $this->footer = view('lootbox::admin.footer');

        $this->modals = Views::factory();

        $this->scripts_in_footer = Scripts::factory([
            'assets/lootbox/lootbox.js',

            'assets/inspinia/js/popper.min.js',
            'assets/inspinia/js/bootstrap.min.js',
            'assets/inspinia/js/plugins/metisMenu/jquery.metisMenu.js',
            'assets/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js',
            'assets/inspinia/js/inspinia.js',
            'assets/inspinia/js/plugins/pace/pace.min.js',
        ]);
    }

    public function plugin($name): Template
    {
        if (is_array($name)) {
            foreach ($name as $plugin) {
                $this->activatePlugin($plugin);
            }
        } elseif (is_string($name)) {
            $this->activatePlugin($name);
        } else {
            throw new \InvalidArgumentException('$name must be a string or an array.');
        }

        return $this;
    }

    private function activatePlugin(string $name): void
    {
        $asset_prefix = 'assets/inspinia/';
        switch ($name) {
            case 'datepicker':
                $this->styles[] = $asset_prefix . 'css/plugins/datapicker/datepicker3.css';
                $this->scripts_in_footer[] = $asset_prefix . 'js/plugins/datapicker/bootstrap-datepicker.js';
                break;
            case 'tagsinput':
                $this->styles[] = $asset_prefix . 'css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css';
                $this->scripts_in_footer[] = $asset_prefix . 'js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js';
                // break;
            case 'typeahead':
                $this->styles[] = $asset_prefix . 'css/plugins/typeahead/typeahead.css';
                $this->scripts_in_footer[] = $asset_prefix . 'js/plugins/typeahead/typeahead.bundle.js';
                break;
            case 'clockpicker':
                $this->styles[] = $asset_prefix . 'css/plugins/clockpicker/clockpicker.css';
                $this->scripts_in_footer[] = $asset_prefix . 'js/plugins/clockpicker/clockpicker.js';
                break;
            case 'icheck':
                $this->styles[] = $asset_prefix . 'css/plugins/iCheck/custom.css';
                $this->scripts_in_footer[] = $asset_prefix . 'js/plugins/iCheck/icheck.min.js';
                break;
        }
    }
}
