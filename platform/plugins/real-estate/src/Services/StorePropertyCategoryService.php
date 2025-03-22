<?php

namespace Srapid\RealEstate\Services;

use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Services\Abstracts\StorePropertyCategoryServiceAbstract;
use Illuminate\Http\Request;

class StorePropertyCategoryService extends StorePropertyCategoryServiceAbstract
{

    /**
     * @param Request $request
     * @param Property $property
     * @return mixed|void
     */
    public function execute(Request $request, Property $property)
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $property->categories()->sync($categories);
            } else {
                $property->categories()->detach();
            }
        }
    }
}
