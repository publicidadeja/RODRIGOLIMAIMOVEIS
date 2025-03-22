<?php

namespace Srapid\Blog;

use Srapid\Blog\Models\Category;
use Srapid\Blog\Models\Tag;
use Srapid\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Srapid\Menu\Repositories\Interfaces\MenuNodeInterface;
use Illuminate\Support\Facades\Schema;
use Srapid\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('posts_translations');
        Schema::dropIfExists('categories_translations');
        Schema::dropIfExists('tags_translations');

        app(DashboardWidgetInterface::class)->deleteBy(['name' => 'widget_posts_recent']);

        app(MenuNodeInterface::class)->deleteBy(['reference_type' => Category::class]);
        app(MenuNodeInterface::class)->deleteBy(['reference_type' => Tag::class]);
    }
}
