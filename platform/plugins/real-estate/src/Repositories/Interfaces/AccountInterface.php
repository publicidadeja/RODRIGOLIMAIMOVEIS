<?php

namespace Srapid\RealEstate\Repositories\Interfaces;

use Srapid\Support\Repositories\Interfaces\RepositoryInterface;

interface AccountInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param int|null $id
     * @return string
     */
    public function createUsername($name, $id = null);
}
