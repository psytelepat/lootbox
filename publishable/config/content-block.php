<?php

return [

    'route' => [
        'prefix' => 'admin/content-block',
        'alias' => 'lootbox.content-block.',
    ],

    'controllers' => [
        'namespace' => '\\Psytelepat\\Lootbox\\Http\\Controllers\\ContentBlock',
    ],

    'views' => [
        'prefix' => 'lootbox::content-block.',
    ],

    'storage' => [
        'disk' => 'public',
    ],

    'cfg' => [
        0 => [
            'name' => 'default',
            'trg' => 1,
            'block_table' => 'content_block',
            'block_modes' => [
                1 => 'text',
                2 => 'photo',
                3 => 'gallery',
                4 => 'video',
                5 => 'quote',
                6 => 'subscription',
                7 => 'double-column',
            ],
        ],
    ],
];
