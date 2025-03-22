<?php

namespace Srapid\RealEstate\Repositories\Interfaces;

use Srapid\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface AccountActivityLogInterface extends RepositoryInterface
{
    /**
     * @param $accountId
     * @param int $paginate
     * @return Collection
     */
    public function getAllLogs($accountId, $paginate = 10);
}
