<?php

use Srapid\RealEstate\Models\Category;
use Srapid\RealEstate\Models\Project;
use Srapid\RealEstate\Models\Property;

Route::group(['namespace' => 'Srapid\RealEstate\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group([
        'prefix'     => BaseHelper::getAdminPrefix() . '/real-estate',
        'middleware' => 'auth',
    ], function () {

        Route::get('settings', [
            'as'   => 'real-estate.settings',
            'uses' => 'RealEstateController@getSettings',
        ]);

        Route::post('settings', [
            'as'         => 'real-estate.settings.post',
            'uses'       => 'RealEstateController@postSettings',
            'permission' => 'real-estate.settings',
        ]);
        
        Route::post('toggle-watermark', [
            'as'         => 'toggle-watermark',
            'uses'       => 'PropertyController@toggleWatermark',
            'permission' => false,
        ]);
        
        Route::group(['prefix' => 'zap-imoveis', 'as' => 'zap-imoveis.'], function () {
            Route::get('', [
                'as'         => 'index',
                'uses'       => 'ZapImoveisController@index',
                'permission' => 'property.index',
            ]);
            
            Route::get('download', [
                'as'         => 'download',
                'uses'       => 'ZapImoveisController@download',
                'permission' => 'property.index',
            ]);
            
            Route::post('generate', [
                'as'         => 'generate',
                'uses'       => 'ZapImoveisController@generate',
                'permission' => 'property.index',
            ]);
        });

        Route::group(['prefix' => 'crm', 'as' => 'crm.'], function () {
            Route::resource('', 'CrmController')
                ->parameters(['' => 'crm']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CrmController@deletes',
                'permission' => 'crm.destroy',
            ]);
            
            Route::get('match-properties/{id}', [
                'as'         => 'match-properties',
                'uses'       => 'CrmController@matchProperties',
                'permission' => 'crm.edit',
            ]);
            
            Route::post('check-property-matches', [
                'as'         => 'check-property-matches',
                'uses'       => 'CrmController@checkPropertyMatches',
                'permission' => 'crm.edit',
            ]);
        });

        Route::group(['prefix' => 'properties', 'as' => 'property.'], function () {
            Route::resource('', 'PropertyController')
                ->parameters(['' => 'property']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'PropertyController@deletes',
                'permission' => 'property.destroy',
            ]);
        });

        Route::group(['prefix' => 'projects', 'as' => 'project.'], function () {
            Route::resource('', 'ProjectController')
                ->parameters(['' => 'project']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProjectController@deletes',
                'permission' => 'project.destroy',
            ]);
        });

        Route::group(['prefix' => 'property-features', 'as' => 'property_feature.'], function () {
            Route::resource('', 'FeatureController')
                ->parameters(['' => 'property_feature']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FeatureController@deletes',
                'permission' => 'property_feature.destroy',
            ]);
        });

        Route::group(['prefix' => 'investors', 'as' => 'investor.'], function () {
            Route::resource('', 'InvestorController')
                ->parameters(['' => 'investor']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'InvestorController@deletes',
                'permission' => 'investor.destroy',
            ]);
        });

        Route::group(['prefix' => 'consults', 'as' => 'consult.'], function () {
            Route::resource('', 'ConsultController')
                ->parameters(['' => 'consult'])
                ->except(['create', 'store']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ConsultController@deletes',
                'permission' => 'consult.destroy',
            ]);
        });

        Route::group(['prefix' => 'categories', 'as' => 'property_category.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CategoryController@deletes',
                'permission' => 'property_category.destroy',
            ]);
        });

        Route::group(['prefix' => 'facilities', 'as' => 'facility.'], function () {
            Route::resource('', 'FacilityController')
                ->parameters(['' => 'facility']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FacilityController@deletes',
                'permission' => 'facility.destroy',
            ]);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'account.'], function () {

            Route::resource('', 'AccountController')
                ->parameters(['' => 'account']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AccountController@deletes',
                'permission' => 'account.destroy',
            ]);

            Route::get('list', [
                'as'         => 'list',
                'uses'       => 'AccountController@getList',
                'permission' => 'account.index',
            ]);

            Route::post('credits/{id}', [
                'as'         => 'credits.add',
                'uses'       => 'TransactionController@postCreate',
                'permission' => 'account.edit',
            ]);
        });

        Route::group(['prefix' => 'packages', 'as' => 'package.'], function () {
            Route::resource('', 'PackageController')
                ->parameters(['' => 'package']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'PackageController@deletes',
                'permission' => 'package.destroy',
            ]);
        });

    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get(SlugHelper::getPrefix(Project::class, 'projects'), 'PublicController@getProjects')
                ->name('public.projects');

            Route::get(SlugHelper::getPrefix(Project::class, 'projects') . '/{slug}', 'PublicController@getProject');

            Route::get(SlugHelper::getPrefix(Property::class, 'properties'), 'PublicController@getProperties')
                ->name('public.properties');

            Route::get(SlugHelper::getPrefix(Category::class, 'property-category') . '/{slug}',
                'PublicController@getPropertyCategory')
                ->name('public.property-category');

            Route::get(SlugHelper::getPrefix(Property::class, 'properties') . '/{slug}',
                'PublicController@getProperty');

            Route::post('send-consult', 'PublicController@postSendConsult')
                ->name('public.send.consult');

            Route::get('currency/switch/{code?}', [
                'as'   => 'public.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);

            Route::group(['as' => 'public.account.'], function () {

                Route::group(['middleware' => ['account.guest']], function () {
                    Route::get('login', 'LoginController@showLoginForm')
                        ->name('login');
                    Route::post('login', 'LoginController@login')
                        ->name('login.post');

                    Route::get('register', 'RegisterController@showRegistrationForm')
                        ->name('register');
                    Route::post('register', 'RegisterController@register')
                        ->name('register.post');

                    Route::get('verify', 'RegisterController@getVerify')
                        ->name('verify');

                    Route::get('password/request',
                        'ForgotPasswordController@showLinkRequestForm')
                        ->name('password.request');
                    Route::post('password/email',
                        'ForgotPasswordController@sendResetLinkEmail')
                        ->name('password.email');
                    Route::post('password/reset', 'ResetPasswordController@reset')
                        ->name('password.update');
                    Route::get('password/reset/{token}',
                        'ResetPasswordController@showResetForm')
                        ->name('password.reset');
                });

                Route::group([
                    'middleware' => [
                        setting('verify_account_email',
                            config('plugins.real-estate.real-estate.verify_email')) ? 'account.guest' : 'account',
                    ],
                ], function () {
                    Route::get('register/confirm/resend',
                        'RegisterController@resendConfirmation')
                        ->name('resend_confirmation');
                    Route::get('register/confirm/{user}', 'RegisterController@confirm')
                        ->name('confirm');
                });
            });

            Route::get('feed/properties', [
                'as'   => 'feeds.properties',
                'uses' => 'PublicController@getPropertyFeeds',
            ]);
        });

        Route::group(['middleware' => ['account'], 'as' => 'public.account.'], function () {
            Route::group(['prefix' => 'account'], function () {

                Route::post('logout', 'LoginController@logout')
                    ->name('logout');

                Route::get('dashboard', [
                    'as'   => 'dashboard',
                    'uses' => 'PublicAccountController@getDashboard',
                ]);

                Route::get('settings', [
                    'as'   => 'settings',
                    'uses' => 'PublicAccountController@getSettings',
                ]);

                Route::post('settings', [
                    'as'   => 'post.settings',
                    'uses' => 'PublicAccountController@postSettings',
                ]);

                Route::get('security', [
                    'as'   => 'security',
                    'uses' => 'PublicAccountController@getSecurity',
                ]);

                Route::put('security', [
                    'as'   => 'post.security',
                    'uses' => 'PublicAccountController@postSecurity',
                ]);

                Route::post('avatar', [
                    'as'   => 'avatar',
                    'uses' => 'PublicAccountController@postAvatar',
                ]);

                Route::get('packages', [
                    'as'   => 'packages',
                    'uses' => 'PublicAccountController@getPackages',
                ]);

                Route::get('transactions', [
                    'as'   => 'transactions',
                    'uses' => 'PublicAccountController@getTransactions',
                ]);

            });

            Route::group(['prefix' => 'account/ajax'], function () {
                Route::get('activity-logs', [
                    'as'   => 'activity-logs',
                    'uses' => 'PublicAccountController@getActivityLogs',
                ]);

                Route::get('transactions', [
                    'as'   => 'ajax.transactions',
                    'uses' => 'PublicAccountController@ajaxGetTransactions',
                ]);

                Route::post('upload', [
                    'as'   => 'upload',
                    'uses' => 'PublicAccountController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as'   => 'upload-from-editor',
                    'uses' => 'PublicAccountController@postUploadFromEditor',
                ]);
                
                Route::post('toggle-watermark', [
                    'as'   => 'public.toggle-watermark',
                    'uses' => 'AccountPropertyController@toggleWatermark',
                ]);

                Route::get('packages', 'PublicAccountController@ajaxGetPackages')
                    ->name('ajax.packages');
                Route::put('packages', 'PublicAccountController@ajaxSubscribePackage')
                    ->name('ajax.package.subscribe');
            });

            Route::group(['prefix' => 'account/properties', 'as' => 'properties.'], function () {
                Route::resource('', 'AccountPropertyController')
                    ->parameters(['' => 'property']);

                Route::post('renew/{id}', [
                    'as'   => 'renew',
                    'uses' => 'AccountPropertyController@renew',
                ]);
            });
            
            // CRM para corretores
            Route::group(['prefix' => 'account/crm', 'as' => 'crm.'], function () {
                Route::get('', [
                    'as'   => 'index',
                    'uses' => 'AccountCrmController@index',
                ]);
                
                Route::get('create', [
                    'as'   => 'create',
                    'uses' => 'AccountCrmController@create',
                ]);
                
                Route::post('', [
                    'as'   => 'store',
                    'uses' => 'AccountCrmController@store',
                ]);
                
                Route::get('{id}/edit', [
                    'as'   => 'edit',
                    'uses' => 'AccountCrmController@edit',
                ]);
                
                Route::put('{id}', [
                    'as'   => 'update',
                    'uses' => 'AccountCrmController@update',
                ]);
                
                Route::delete('{id}', [
                    'as'   => 'destroy',
                    'uses' => 'AccountCrmController@destroy',
                ]);
                
                Route::get('{id}/match-properties', [
                    'as'   => 'match-properties',
                    'uses' => 'AccountCrmController@matchProperties',
                ]);
            });

            Route::group(['prefix' => 'account'], function () {
                Route::get('packages/{id}/subscribe', 'PublicAccountController@getSubscribePackage')
                    ->name('package.subscribe');

                Route::get('packages/{id}/subscribe/callback',
                    'PublicAccountController@getPackageSubscribeCallback')
                    ->name('package.subscribe.callback');
            });
        });
    }
});