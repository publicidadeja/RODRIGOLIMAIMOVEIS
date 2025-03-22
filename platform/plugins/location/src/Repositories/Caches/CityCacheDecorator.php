<?php

namespace Srapid\Location\Repositories\Caches;

use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;
use Srapid\Location\Repositories\Interfaces\CityInterface;

class CityCacheDecorator extends CacheAbstractDecorator implements CityInterface
{
    /**
     * {@inheritDoc}
     */
    public function filters($keyword, $limit = 10, array $with = [], array $select = ['cities.*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
