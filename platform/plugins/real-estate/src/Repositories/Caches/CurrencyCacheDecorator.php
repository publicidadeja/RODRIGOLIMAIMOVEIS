<?php

namespace Srapid\RealEstate\Repositories\Caches;

use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;
use Srapid\RealEstate\Repositories\Interfaces\CurrencyInterface;

class CurrencyCacheDecorator extends CacheAbstractDecorator implements CurrencyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllCurrencies()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
