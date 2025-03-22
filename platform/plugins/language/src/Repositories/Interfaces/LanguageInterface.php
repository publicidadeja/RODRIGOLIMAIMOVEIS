<?php

namespace Srapid\Language\Repositories\Interfaces;

use Srapid\Support\Repositories\Interfaces\RepositoryInterface;

interface LanguageInterface extends RepositoryInterface
{
    /**
     * @param array $select
     * @return mixed
     *
     * @since 2.1
     */
    public function getActiveLanguage($select = ['*']);

    /**
     * @param array $select
     * @return mixed
     *
     * @since 2.2
     */
    public function getDefaultLanguage($select = ['*']);
}
