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
        'EUR' => 1,
        'USD' => 1.1497,
        'JPY' => 129.53
    ];

    /**
     * The base currency we use fo exchanging
     *
     */
    protected string $base = 'EUR';
}
