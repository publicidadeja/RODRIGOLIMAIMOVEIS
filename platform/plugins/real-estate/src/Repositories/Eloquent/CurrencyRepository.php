<?php

namespace Srapid\RealEstate\Repositories\Eloquent;

use Srapid\Support\Repositories\Eloquent\RepositoriesAbstract;
use Srapid\RealEstate\Repositories\Interfaces\CurrencyInterface;

class CurrencyRepository extends RepositoriesAbstract implements CurrencyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllCurrencies()
    {
        $data = $this->model
            ->orderBy('order', 'ASC')
            ->get();

        $this->resetModel();

        return $data;
    }
}
