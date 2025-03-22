<?php

namespace Srapid\ACL\Repositories\Caches;

use Srapid\ACL\Repositories\Interfaces\RoleInterface;
use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;

class RoleCacheDecorator extends CacheAbstractDecorator implements RoleInterface
{
    /**
     * {@inheritDoc}
     */
    public function createSlug($name, $id)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }
}
