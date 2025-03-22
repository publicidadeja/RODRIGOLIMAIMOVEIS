<?php

namespace Srapid\RealEstate\Repositories\Eloquent;

use Srapid\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Srapid\Support\Repositories\Eloquent\RepositoriesAbstract;

class AccountActivityLogRepository extends RepositoriesAbstract implements AccountActivityLogInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllLogs($accountId, $paginate = 10)
    {
        return $this->model
            ->where('account_id', $accountId)
            ->latest('created_at')
            ->paginate($paginate);
    }
}
