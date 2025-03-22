<?php

namespace Srapid\Location\Models;

use Srapid\Base\Models\BaseModel;

class CityTranslation extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cities_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'lang_code',
        'cities_id',
        'name',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
