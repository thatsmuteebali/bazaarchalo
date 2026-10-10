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
        'flat_rate' => 250,
        'free_over' => 5000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment methods
    |--------------------------------------------------------------------------
    | The array key is what the checkout form sends as "payment_method".
    */
    'payment_methods' => [
        'cod' => [
            'label'       => 'Cash on Delivery',
            'description' => 'Pay in cash when your order is delivered to your door.',
            'icon'        => 'fa-money-bill-wave',
        ],
        'online' => [
            'label'       => 'Online Payment',
            'description' => 'Pay online with a card or mobile wallet. You will complete the payment on a secure page after placing your order.',
            'icon'        => 'fa-credit-card',
        ],
    ],

    'provinces' => [
        'Punjab',
        'Sindh',
        'Khyber Pakhtunkhwa',
        'Balochistan',
        'Islamabad Capital Territory',
        'Azad Jammu & Kashmir',
        'Gilgit-Baltistan',
    ],

    // suggestions only: customers can type any other city
    'cities' => [
        'Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta',
        'Hyderabad', 'Gujranwala', 'Sialkot', 'Bahawalpur', 'Sargodha', 'Sukkur', 'Larkana', 'Sahiwal',
        'Gujrat', 'Rahim Yar Khan', 'Jhelum', 'Abbottabad', 'Mardan', 'Mingora', 'Muzaffarabad', 'Mirpur', 'Gilgit',
    ],
];
