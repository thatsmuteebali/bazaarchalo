<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    | Shown in front of every price ("Rs. 2,500").
    */
    'currency_symbol' => 'Rs.',

    /*
    |--------------------------------------------------------------------------
    | Delivery charges  (PLACEHOLDER VALUES - set your real charges)
    |--------------------------------------------------------------------------
    | flat_rate : delivery charge for every order
    | free_over : orders at or above this subtotal get free delivery (null = never free)
    */
    'shipping' => [
        'flat_rate' => 0,
        'free_over' => 0,
    ],

];
