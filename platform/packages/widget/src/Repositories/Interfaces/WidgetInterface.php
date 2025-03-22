<?php

namespace Srapid\Widget\Repositories\Interfaces;

use Srapid\Support\Repositories\Interfaces\RepositoryInterface;

interface WidgetInterface extends RepositoryInterface
{
    /**
     * Get all theme widgets
     * @param string $theme
     * @return mixed
     */
    public function getByTheme($theme);
}
