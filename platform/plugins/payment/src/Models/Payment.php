<?php

namespace Srapid\Payment\Models;

use Srapid\ACL\Models\User;
use Srapid\Base\Models\BaseModel;
use Srapid\Base\Traits\EnumCastable;
use Srapid\Payment\Enums\PaymentMethodEnum;
use Srapid\Payment\Enums\PaymentStatusEnum;
use Html;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'currency',
        'user_id',
        'charge_id',
        'payment_channel',
        'description',
        'status',
        'order_id',
        'payment_type',
        'customer_id',
        'customer_type',
        'refunded_amount',
        'refund_note',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'payment_channel' => PaymentMethodEnum::class,
        'status'          => PaymentStatusEnum::class,
        'metadata'        => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return MorphTo
     */
    public function customer(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $time = Html::tag('span', $this->created_at->diffForHumans(), ['class' => 'small italic']);

        return __('You have created a payment #:charge_id via :channel :time : :amount', [
            'charge_id' => $this->charge_id,
            'channel'   => $this->payment_channel->label(),
            'time'      => $time,
            'amount'    => number_format($this->amount, 2) . $this->currency,
        ]);
    }
}
