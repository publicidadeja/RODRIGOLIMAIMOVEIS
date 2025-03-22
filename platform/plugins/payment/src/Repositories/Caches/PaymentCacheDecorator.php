<?php

namespace Srapid\Payment\Repositories\Caches;

use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;
use Srapid\Payment\Repositories\Interfaces\PaymentInterface;

class PaymentCacheDecorator extends CacheAbstractDecorator implements PaymentInterface
{

}
