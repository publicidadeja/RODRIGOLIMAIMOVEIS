<?php

namespace Srapid\Widget\Repositories\Eloquent;

use Srapid\Support\Repositories\Eloquent\RepositoriesAbstract;
use Srapid\Widget\Repositories\Interfaces\WidgetInterface;

class WidgetRepository extends RepositoriesAbstract implements WidgetInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByTheme($theme)
    {
        $data = $this->model->where('theme', $theme)->get();
        $this->resetModel();

        return $data;
    }
}
