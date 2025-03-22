<?php

Route::group(['namespace' => 'Srapid\Paystack\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('paystack/payment/callback', [
        'as'   => 'paystack.payment.callback',
        'uses' => 'PaystackController@getPaymentStatus',
    ]);
});
