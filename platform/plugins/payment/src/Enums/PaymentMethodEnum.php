<?php

namespace Srapid\Payment\Enums;

use Srapid\Base\Supports\Enum;
use Srapid\Payment\Services\Gateways\PayPalPaymentService;
use Srapid\Payment\Services\Gateways\StripePaymentService;

/**
 * @method static PaymentMethodEnum STRIPE()
 * @method static PaymentMethodEnum PAYPAL()
 * @method static PaymentMethodEnum COD()
 * @method static PaymentMethodEnum BANK_TRANSFER()
 */
class PaymentMethodEnum extends Enum
{
    public const STRIPE = 'stripe';
    public const PAYPAL = 'paypal';
    public const COD = 'cod';
    public const BANK_TRANSFER = 'bank_transfer';

    /**
     * @var string
     */
    public static $langPath = 'plugins/payment::payment.methods';

    /**
     * @return string
     */
    public function getServiceClass()
    {
        switch ($this->value) {
            case self::PAYPAL:
                return PayPalPaymentService::class;
            case self::STRIPE:
                return StripePaymentService::class;
            default:
                return apply_filters(PAYMENT_FILTER_GET_SERVICE_CLASS, null, $this->value);
        }
    }
}
