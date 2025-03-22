<?php

namespace Srapid\RealEstate\Repositories\Caches;

use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;
use Srapid\RealEstate\Repositories\Interfaces\CategoryInterface;

class CategoryCacheDecorator extends CacheAbstractDecorator implements CategoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCategories(array $select, array $orderBy)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
