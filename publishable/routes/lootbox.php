<?php

Route::group([
    'prefix' => config('lootbox.route.prefix'),
    'as' => config('lootbox.route.alias'),
    'middleware' => ['web'],
], function () {

    $namespacePrefix = '\\Psytelepat\\Lootbox\\Http\\Controllers\\';

    Route::get('login', ['uses' => $namespacePrefix.'LootboxAuthController@login', 'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'LootboxAuthController@postLogin', 'as' => 'postlogin']);
    Route::any('logout', ['uses' => $namespacePrefix.'LootboxAuthController@logout', 'as' => 'logout']);

    Route::group(['middleware' => [ 'admin.user', 'admin.after' ] ], function () use ($namespacePrefix) {

        Route::any('', ['uses' => $namespacePrefix.'LootboxController@index', 'as' => 'dashboard' ]);
        Route::any('profile', ['uses' => $namespacePrefix.'ProfileController@index', 'as' => 'profile' ]);

        function crud_routes($prefix, $namespacePrefix, $controller, $section = null)
        {
            Route::group([
                'as'     => str_replace('/', '.', $prefix).'.',
                'prefix' => str_replace('.', '/', $prefix),
            ], function () use ($namespacePrefix, $controller, $section) {

                $namespacePrefix .= 'Admin\\';

                Route::match(['get','post'], '/', ['uses' => $namespacePrefix.$controller.'@index', 'as' => 'index', 'section' => $section]);
                Route::match(['get','post'], 'create', ['uses' => $namespacePrefix.$controller.'@create', 'as' => 'create', 'section' => $section]);
                Route::match(['get','post'], 'edit/{id}', ['uses' => $namespacePrefix.$controller.'@edit', 'as' => 'edit', 'section' => $section]);
                Route::match(['get','post'], 'repos/{id}', ['uses' => $namespacePrefix.$controller.'@repos', 'as' => 'repos', 'section' => $section]);
                Route::match(['get','post','delete'], 'delete/{id}', ['uses' => $namespacePrefix.$controller.'@delete', 'as' => 'delete', 'section' => $section]);
            });
        }

        // Users Routes
        crud_routes('users', $namespacePrefix, 'AdminUser', 'lootbox.users');

        // Roles Routes
        crud_routes('roles', $namespacePrefix, 'AdminRole', 'lootbox.roles');

        // Permissions Routes
        crud_routes('permissions', $namespacePrefix, 'AdminPermission', 'lootbox.permissions');

        // ApplicatedForms Routes
        crud_routes('applicated-form', $namespacePrefix, 'AdminApplicatedForm', 'lootbox.applicated-form');

        Route::any('settings', ['uses' => $namespacePrefix.'SettingsController@index', 'as' => 'settings.index', 'section' => 'lootbox.settings' ]);

        Route::any('translation', ['uses' => $namespacePrefix.'TranslationController@index', 'as' => 'translation.index', 'section' => 'lootbox.translation' ]);
        Route::any('translation/typo', ['uses' => $namespacePrefix.'TranslationController@typo', 'as' => 'translation.typo', 'section' => 'lootbox.translation' ]);
        Route::any('translation/form/{lang}/{file}/{folder?}', ['uses' => $namespacePrefix.'TranslationController@form', 'as' => 'translation.form', 'section' => 'lootbox.translation' ]);
        Route::any('translation/update/{lang}/{file}/{folder?}', ['uses' => $namespacePrefix.'TranslationController@update', 'as' => 'translation.update', 'section' => 'lootbox.translation' ]);

        Route::group([
            'prefix' => 'upload',
            'as' => 'upload.',
        ], function () use ($namespacePrefix) {
            Route::any('{target}/{id?}', ['uses' => $namespacePrefix.'UploadController@view', 'as' => 'base']);
            Route::post('{target}/{id}/upload/{uploadMode}', ['uses' => $namespacePrefix.'UploadController@upload', 'as' => 'upload']);
            Route::any('{target}/{id}/repos/{from}/{to}', ['uses' => $namespacePrefix.'UploadController@repos', 'as' => 'repos']);
            Route::match(['get','post'], '{target}/{id}/edit/{fileID}', ['uses' => $namespacePrefix.'UploadController@edit', 'as' => 'edit']);
            Route::match(['get','post'], '{target}/{id}/crop/{fileID}', ['uses' => $namespacePrefix.'UploadController@crop', 'as' => 'crop']);
            Route::post('{target}/{id}/delete', ['uses' => $namespacePrefix.'UploadController@delete', 'as' => 'delete']);
        });
    });
});

Route::group([
    'namespace' => config('content-block.controllers.namespace'),
    'prefix' => config('content-block.route.prefix'),
    'as' => config('content-block.route.alias'),
    'middleware' => [ 'web', 'admin.user', 'admin.after' ],
], function () {

    Route::get('', ['uses' => 'ContentBlockController@index', 'as' => 'base']);
    Route::get('{trg}/{usr}', ['uses' => 'ContentBlockController@index', 'as' => 'index']);

    Route::get('{trg}/{usr}/{grp}', ['uses' => 'ContentBlockController@view', 'as' => 'block.base']);
    Route::any('{trg}/{usr}/create/{mode}', ['uses' => 'ContentBlockController@create', 'as' => 'block.create']);
    Route::any('{trg}/{usr}/{grp}/repos/{to}', ['uses' => 'ContentBlockController@repos', 'as' => 'block.repos']);
    Route::match(['get','post'], '{trg}/{usr}/{grp}/edit', ['uses' => 'ContentBlockController@edit', 'as' => 'block.edit']);
    Route::post('{trg}/{usr}/{grp}/delete', ['uses' => 'ContentBlockController@delete', 'as' => 'block.delete']);

    Route::any('{trg}/{usr}/{grp}/upload/{target}', ['uses' => 'ContentBlockUploadController@upload', 'as' => 'upload.base']);
    Route::any('{trg}/{usr}/{grp}/upload/{target}/upload/{uploadMode}', ['uses' => 'ContentBlockUploadController@upload', 'as' => 'upload.upload']);
    Route::any('{trg}/{usr}/{grp}/upload/{target}/repos/{from_id}/{to_id}', ['uses' => 'ContentBlockUploadController@repos', 'as' => 'upload.repos']);
    Route::match(['get','post'], '{trg}/{usr}/{grp}/upload/{target}/edit/{fileID}', ['uses' => 'ContentBlockUploadController@edit', 'as' => 'upload.edit']);
    Route::match(['get','post'], '{trg}/{usr}/{grp}/upload/{target}/crop/{fileID}', ['uses' => 'ContentBlockUploadController@crop', 'as' => 'upload.edit']);
    Route::post('{trg}/{usr}/{grp}/upload/{target}/delete', ['uses' => 'ContentBlockUploadController@delete', 'as' => 'upload.delete']);
});

Route::group([
    'namespace' => config('publicator.controllers.namespace'),
    'prefix' => config('publicator.route.prefix'),
    'as' => config('publicator.route.alias'),
    'middleware' => [ 'web', 'admin.user', 'admin.after' ],
], function () {

    Route::get('', ['uses' => 'PublicatorAdminController@index', 'as' => 'index', 'section' => 'publicator' ]);
    Route::get('tags', ['uses' => 'PublicatorAdminController@tags', 'as' => 'tags.index', 'section' => 'publicator.tags' ]);

    Route::group([
        'prefix' => 'post',
        'as' => 'post.',
    ], function () {
        $section = 'publicator.post';
        Route::get('', ['uses' => 'PublicatorAdminController@index', 'as' => 'index', 'section' => $section ]);
        Route::get('with-tag/{slug}', ['uses' => 'PublicatorAdminController@withTag', 'as' => 'with-tag', 'section' => $section ]);
        Route::get('with-category/{id}', ['uses' => 'PublicatorAdminController@withCategory', 'as' => 'with-category', 'section' => $section ]);
        Route::match(['get','post'], 'create', ['uses' => 'PublicatorAdminController@create', 'as' => 'create', 'section' => $section ]);
        Route::match(['get','post'], 'edit/{id}', ['uses' => 'PublicatorAdminController@edit', 'as' => 'edit', 'section' => $section ]);
        Route::match(['get','post'], 'repos/{id}', ['uses' => 'PublicatorAdminController@repos', 'as' => 'repos', 'section' => $section ]);
        Route::match(['get','post','delete'], 'delete/{id}', ['uses' => 'PublicatorAdminController@delete', 'as' => 'delete', 'section' => $section ]);
    });

    Route::group([
        'prefix' => 'category',
        'as' => 'category.',
    ], function () {
        $section = 'publicator.category';
        Route::get('', ['uses' => 'PublicatorAdminCategoryController@index', 'as' => 'index', 'section' => $section ]);
        Route::match(['get','post'], 'create', ['uses' => 'PublicatorAdminCategoryController@create', 'as' => 'create', 'section' => $section ]);
        Route::match(['get','post'], 'edit/{id}', ['uses' => 'PublicatorAdminCategoryController@edit', 'as' => 'edit', 'section' => $section ]);
        Route::match(['get','post'], 'repos/{id}', ['uses' => 'PublicatorAdminCategoryController@repos', 'as' => 'repos', 'section' => $section ]);
        Route::match(['get','post','delete'], 'delete/{id}', ['uses' => 'PublicatorAdminCategoryController@delete', 'as' => 'delete' , 'section' => $section ]);
    });

    Route::group([
        'prefix' => 'author',
        'as' => 'author.',
    ], function () {
        $section = 'publicator.author';
        Route::get('', ['uses' => 'PublicatorAdminAuthorController@index', 'as' => 'index', 'section' => $section ]);
        Route::match(['get','post'], 'create', ['uses' => 'PublicatorAdminAuthorController@create', 'as' => 'create', 'section' => $section ]);
        Route::match(['get','post'], 'edit/{id}', ['uses' => 'PublicatorAdminAuthorController@edit', 'as' => 'edit', 'section' => $section ]);
        Route::match(['get','post'], 'repos/{id}', ['uses' => 'PublicatorAdminAuthorController@repos', 'as' => 'repos', 'section' => $section ]);
        Route::match(['get','post','delete'], 'delete/{id}', ['uses' => 'PublicatorAdminAuthorController@delete', 'as' => 'delete', 'section' => $section ]);
    });

    Route::group([
        'prefix' => 'subscription',
        'as' => 'subscription.',
    ], function () {
        $section = 'publicator.subscription';
        Route::get('', ['uses' => 'PublicatorAdminSubscriptionController@index', 'as' => 'index', 'section' => $section ]);
        Route::match(['get','post'], 'create', ['uses' => 'PublicatorAdminSubscriptionController@create', 'as' => 'create', 'section' => $section ]);
        Route::match(['get','post'], 'edit/{id}', ['uses' => 'PublicatorAdminSubscriptionController@edit', 'as' => 'edit', 'section' => $section ]);
        Route::match(['get','post'], 'repos/{id}', ['uses' => 'PublicatorAdminSubscriptionController@repos', 'as' => 'repos', 'section' => $section ]);
        Route::match(['get','post','delete'], 'delete/{id}', ['uses' => 'PublicatorAdminSubscriptionController@delete', 'as' => 'delete', 'section' => $section ]);
    });
});
