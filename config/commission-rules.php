<?php

return [
    'deposit' => [
        'private' => [
            'fee' => (0.03/100),
            'max_amount' => null,
            'max_number' => null,
            'duration' => null
        ],

        'business' => [
            'fee' => (0.03/100),
            'max_amount' => null,
            'max_number' => null,
            'duration' => null,
        ],
    ],

    'withdraw' => [
        'private' => [
            'fee' => (0.3/100),
            'max_amount' => 1000,
            'max_number' => 3,
            'duration' => 'week'
        ],

        'business' => [
            'fee' => (0.5/100),
            'max_amount' => null,
            'max_number' => null,
            'duration' => null,
        ],
    ]
];
