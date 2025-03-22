<?php

namespace Srapid\AuditLog\Repositories\Caches;

use Srapid\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;

/**
 * @since 16/09/2016 10:55 AM
 */
class AuditLogCacheDecorator extends CacheAbstractDecorator implements AuditLogInterface
{
}
