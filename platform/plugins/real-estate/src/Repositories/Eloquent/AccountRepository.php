<?php

namespace Srapid\RealEstate\Repositories\Eloquent;

use Srapid\RealEstate\Repositories\Interfaces\AccountInterface;
use Srapid\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Str;

class AccountRepository extends RepositoriesAbstract implements AccountInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUsername($name, $id = null)
    {
        $username = Str::slug($name);
        $index = 1;
        $baseSlug = $username;
        while ($this->model->where('username', $username)->where('id', '!=', $id)->count() > 0) {
            $username = $baseSlug . '-' . $index++;
        }

        if (empty($username)) {
            $username = $baseSlug . '-' . time();
        }

        $this->resetModel();

        return $username;
    }
}
