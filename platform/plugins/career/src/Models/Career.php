<?php

namespace Srapid\Career\Models;

use Srapid\Base\Models\BaseModel;
use Srapid\Base\Traits\EnumCastable;
use Srapid\Base\Enums\BaseStatusEnum;

class Career extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'careers';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'location',
        'salary',
        'description',
        'content',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
