<?php

namespace Srapid\RealEstate\Repositories\Caches;

use Srapid\RealEstate\Repositories\Interfaces\AccountInterface;
use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;

class AccountCacheDecorator extends CacheAbstractDecorator implements AccountInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUsername($name, $id = null)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
