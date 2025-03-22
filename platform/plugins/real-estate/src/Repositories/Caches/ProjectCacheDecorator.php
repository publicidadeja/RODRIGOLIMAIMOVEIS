<?php

namespace Srapid\RealEstate\Repositories\Caches;

use Srapid\RealEstate\Repositories\Interfaces\ProjectInterface;
use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;

class ProjectCacheDecorator extends CacheAbstractDecorator implements ProjectInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProjects($filters = [], $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedProjects(int $projectId, $limit = 4, array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
