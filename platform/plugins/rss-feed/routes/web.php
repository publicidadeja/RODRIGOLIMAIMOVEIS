<?php

Route::group(['namespace' => 'Srapid\RssFeed\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('feed/posts', [
            'as'   => 'feeds.posts',
            'uses' => 'RssFeedController@getPostFeeds',
        ]);

    });
});
