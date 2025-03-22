<?php

namespace Srapid\RealEstate\Models;

use Srapid\Base\Models\BaseModel;

class CategoryTranslation extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_categories_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'lang_code',
        're_categories_id',
        'name',
        'description',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
