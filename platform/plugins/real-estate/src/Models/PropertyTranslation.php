<?php

namespace Srapid\RealEstate\Models;

use Srapid\Base\Models\BaseModel;

class PropertyTranslation extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_properties_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'lang_code',
        're_properties_id',
        'name',
        'description',
        'content',
        'location',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
