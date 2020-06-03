<?php

return [

    'route' => [
        'prefix' => 'admin/publicator',
        'alias' => 'lootbox.publicator.',
    ],

    'controllers' => [
        'namespace' => '\\Psytelepat\\Lootbox\\Http\\Controllers\\Publicator',
    ],

    'views' => [
        'prefix' => 'lootbox::publicator.',
    ],

    'with_categories' => true,
    'with_similars' => false,
    'with_authors' => true,
    'with_tags' => true,

    'with_post_content_field' => false,

];
