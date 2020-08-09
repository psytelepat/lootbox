<?php

return [

    'route' => [
        'prefix' => 'admin',
        'alias' => 'lootbox.',
    ],

    'menu' => [
        'navbar' => [
            [
                'route' => 'lootbox.users.index',
                'icon' => 'fa fa-users',
                'title' => 'lootbox::common.users',
                'items' => [
                    [
                        'section' => 'lootbox.users',
                        'route' => 'lootbox.users.index',
                        'icon' => 'fa fa-users',
                        'title' => 'lootbox::common.users',
                    ],
                    [
                        'section' => 'lootbox.roles',
                        'route' => 'lootbox.roles.index',
                        'icon' => 'fas fa-user-tag',
                        'title' => 'lootbox::common.roles',
                    ],
                    [
                        'section' => 'lootbox.permissions',
                        'route' => 'lootbox.permissions.index',
                        'icon' => 'fa fa-key',
                        'title' => 'lootbox::common.permissions',
                    ],
                ],
            ],
            [
                'route' => 'lootbox.publicator.index',
                'icon' => 'fa fa-newspaper',
                'title' => 'lootbox::publicator.publicator',
                'items' => [
                    [
                        'section' => 'publicator.post',
                        'route' => 'lootbox.publicator.post.index',
                        'icon' => 'fa fa-newspaper',
                        'title' => 'lootbox::publicator.posts',
                    ],
                    [
                        'section' => 'publicator.category',
                        'route' => 'lootbox.publicator.category.index',
                        'icon' => 'fas fa-sitemap',
                        'title' => 'lootbox::publicator.categories',
                    ],
                    [
                        'section' => 'publicator.author',
                        'route' => 'lootbox.publicator.author.index',
                        'icon' => 'fas fa-users',
                        'title' => 'lootbox::publicator.authors',
                    ],
                    [
                        'section' => 'publicator.tags',
                        'route' => 'lootbox.publicator.tags.index',
                        'icon' => 'fa fa-tags',
                        'title' => 'lootbox::publicator.tags',
                    ],
                    [
                        'section' => 'publicator.subscription',
                        'route' => 'lootbox.publicator.subscription.index',
                        'icon' => 'fa fa-envelope',
                        'title' => 'lootbox::publicator.subscriptions',
                    ],
                ],
            ],
            [
                'section' => 'lootbox.settings',
                'route' => 'lootbox.settings.index',
                'icon' => 'fa fa-cog',
                'title' => 'lootbox::common.settings',
            ],
        ],
    ],

    'uploads' => [
        'user' => [
            'avatar' => [
                'handle' => 'avatar',
                'object' => '\App\User',
                'upload' => '\Psytelepat\Lootbox\User\Avatar',
            ],
        ],
        'publicator' => [
            'post' => [
                'cover' => [
                    'handle' => 'cover',
                    'object' => '\Psytelepat\Lootbox\Publicator\PublicatorPost',
                    'upload' => '\Psytelepat\Lootbox\Publicator\PublicatorCover',
                ],
                'seo' => [
                    'handle' => 'seo',
                    'object' => '\Psytelepat\Lootbox\Publicator\PublicatorPost',
                    'upload' => '\Psytelepat\Lootbox\Publicator\SEOImage',
                ],
            ],
            'author' => [
                'avatar' => [
                    'handle' => 'avatar',
                    'object' => '\Psytelepat\Lootbox\Publicator\PublicatorAuthor',
                    'upload' => '\Psytelepat\Lootbox\Publicator\PublicatorAuthorAvatar',
                ],
            ]
        ],
        'content-block' => [
            'photo' => [
                'handle' => 'photo',
                'object' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockModel',
                'upload' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockImage',
            ],
            'gallery' => [
                'handle' => 'gallery',
                'object' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockModel',
                'upload' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockImage',
            ],
            'quote' => [
                'handle' => 'quote',
                'object' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockModel',
                'upload' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockQuoteImage',
            ],
            'video' => [
                'handle' => 'video',
                'object' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockModel',
                'upload' => '\Psytelepat\Lootbox\ContentBlock\ContentBlockVideoImage',
            ],
        ],
    ],

    'images' => [
        'Psytelepat\Lootbox\ContentBlock\BaseImage' => [
            [ 'size' => 1, 'w' => 1920, 'h' => 1280, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 1280, 'h' => 1024, 'mode' => 'default', ],
            [ 'size' => 3, 'w' => 800,  'h' => 600,  'mode' => 'default', ],
            [ 'size' => 4, 'w' => 100,  'h' => 100,  'mode' => 'fit', ],
        ],
        'Psytelepat\Lootbox\ContentBlock\ContentBlockImage' => [
            [ 'size' => 1, 'w' => 1920, 'h' => 1080, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 960, 'h' => 540, 'mode' => 'default', ],
            [ 'size' => 3, 'w' => 400, 'h' => 225,  'mode' => 'default', ],
            [ 'size' => 4, 'w' => 200, 'h' => 112,  'mode' => 'default', ],
        ],
        'Psytelepat\Lootbox\ContentBlock\ContentQuoteImage' => [
            [ 'size' => 1, 'w' => 1920, 'h' => 1080, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 960, 'h' => 540, 'mode' => 'default', ],
            [ 'size' => 3, 'w' => 400, 'h' => 225,  'mode' => 'default', ],
            [ 'size' => 4, 'w' => 200, 'h' => 112,  'mode' => 'default', ],
        ],
        'Psytelepat\Lootbox\ContentBlock\ContentVideoImage' => [
            [ 'size' => 1, 'w' => 1920, 'h' => 1080, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 960, 'h' => 540, 'mode' => 'default', ],
            [ 'size' => 3, 'w' => 400, 'h' => 225,  'mode' => 'default', ],
            [ 'size' => 4, 'w' => 200, 'h' => 112,  'mode' => 'default', ],
        ],
        'Psytelepat\Lootbox\User\Avatar' => [
            [ 'size' => 1, 'w' => 400, 'h' => 400, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 100,  'h' => 100,  'mode' => 'fit', ],
        ],
        'Psytelepat\Lootbox\Publicator\PublicatorAuthorAvatar' => [
            [ 'size' => 1, 'w' => 400, 'h' => 400, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 100,  'h' => 100,  'mode' => 'fit', ],
        ],
        'Psytelepat\Lootbox\Publicator\PublicatorCover' => [
            [ 'size' => 1, 'w' => 1920, 'h' => 1080, 'mode' => 'default', ],
            [ 'size' => 2, 'w' => 960, 'h' => 540, 'mode' => 'default', ],
            [ 'size' => 3, 'w' => 400, 'h' => 225,  'mode' => 'default', ],
            [ 'size' => 4, 'w' => 200, 'h' => 112,  'mode' => 'default', ],
        ],
        'Psytelepat\Lootbox\Publicator\SEOImage' => [
            [ 'size' => 1, 'w' => 960,  'h' => 540,  'mode' => 'default', ],
            [ 'size' => 2, 'w' => 100,  'h' => 100,  'mode' => 'fit', ],
        ],
    ],

    'settings' => [
        'list' => [
            'main' => [
                'title' => 'Основные настройки',
                'fields' => [
                    'site_title' => [
                        'title' => 'Название сайта',
                    ],
                    'page_title' => [
                        'title' => 'Page title',
                    ],
                    'domain' => [
                        'title' => 'Домен',
                    ],
                    'email_callback' => [
                        'title' => 'Обратная связь',
                    ],
                    'email' => [
                        'title' => 'E-mail',
                    ],
                    'phone' => [
                        'title' => 'Телефон',
                    ],
                    'telegram_url' => [
                        'title' => 'Telegram',
                    ],
                    'facebook_url' => [
                        'title' => 'Facebook',
                    ],
                    'instagram_url' => [
                        'title' => 'Instagram',
                    ],
                    'youtube_url' => [
                        'title' => 'Youtube',
                    ],
                    'vkontakte_url' => [
                        'title' => 'Вконтакте',
                    ],
                    'twitter_url' => [
                        'title' => 'Twitter',
                    ],
                ]
            ],
        ],
    ],

];
