<?php

namespace Srapid\Location\Models;

use Srapid\Base\Traits\EnumCastable;
use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'state_id',
        'country_id',
        'record_id',
        'order',
        'is_default',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class)->withDefault();
    }
}
