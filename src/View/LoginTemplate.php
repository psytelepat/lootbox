<?php

namespace Psytelepat\Lootbox\View;

use Psytelepat\Lootbox\View\Template;
use Psytelepat\Lootbox\View\Scripts;
use Psytelepat\Lootbox\View\Styles;

class LoginTemplate extends Template
{

    protected $layout = 'lootbox::layout.login';
    protected $properties = [
        'styles',
        'scripts_in_header',
        'title',
        'scripts_in_footer',
    ];
    
    public $styles;
    public $scripts_in_header;
    public $title;
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
}
