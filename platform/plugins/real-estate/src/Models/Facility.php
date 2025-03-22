<?php

namespace Srapid\RealEstate\Models;

use Srapid\Base\Traits\EnumCastable;
use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\Base\Models\BaseModel;

class Facility extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_facilities';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
