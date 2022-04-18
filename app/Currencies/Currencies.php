<?php


namespace App\Currencies;


class Currencies extends AbstractCurrencies
{

    /**
     * The list of currencies we use in our services
     *
     * @var string[]
     */
    protected array $rates = [
        'USD' => 1.1497,
        'JPY' => 129.53
    ];

    /**
     * The base currency we use fo exchanging
     *
     * @var array
     */
    protected array $base = [
        'EUR' => 1
    ];
}
