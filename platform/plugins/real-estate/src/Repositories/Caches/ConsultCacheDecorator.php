<?php

namespace Srapid\RealEstate\Repositories\Caches;

use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;
use Srapid\RealEstate\Repositories\Interfaces\ConsultInterface;

class ConsultCacheDecorator extends CacheAbstractDecorator implements ConsultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUnread($select = ['*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function countUnread()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
