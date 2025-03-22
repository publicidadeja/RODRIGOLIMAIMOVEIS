<?php

namespace Srapid\RealEstate\Services\Abstracts;

use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Repositories\Interfaces\CategoryInterface;
use Illuminate\Http\Request;

abstract class StorePropertyCategoryServiceAbstract
{
    /**
     * @var CategoryInterface
     */
    protected $categoryRepository;

    /**
     * StorePropertyCategoryServiceAbstract constructor.
     * @param CategoryInterface $categoryRepository
     */
    public function __construct(CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @param Property $property
     * @return mixed
     */
    abstract public function execute(Request $request, Property $property);
}
