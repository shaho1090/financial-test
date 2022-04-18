<?php


namespace App\Currencies;


use App\Exceptions\CurrencyNotFoundException;
use App\Exceptions\CurrencyExchangeURLException;
use Illuminate\Support\Facades\Http;

abstract class AbstractCurrencies
{
    protected array $rates = [];

    protected array $base = [];

    private string $exchangeRateURL = '';

    public function getBase(): array
    {
        return $this->base;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return array_merge($this->getBase(), $this->getRates());
    }

    /**
     * @param string $currency
     * @return bool
     */
    public function check(string $currency): bool
    {
        return array_key_exists($currency, $this->getAll());
    }

    /**
     * @throws CurrencyExchangeURLException
     */
    public function setOnlineRates(): static
    {
        $this->rates = $this->getOnlineRates();

        return $this;
    }

    /**
     * @throws CurrencyExchangeURLException
     */
    protected function getOnlineRates()
    {
        $this->setCurrencyExchangeURL();

        $response = Http::get($this->exchangeRateURL);

        return json_decode(json_encode($response->object()->rates), true);
    }

    /**
     * @throws CurrencyNotFoundException
     */
    public function getRateFor($currency)
    {
        if (!$this->check($currency)) {
            throw new CurrencyNotFoundException();
        }

       return $this->getRates()[$currency];
    }

    /**
     * @throws CurrencyExchangeURLException
     */
    protected function setCurrencyExchangeURL()
    {
        $url = config('online-rates.url');

        if (is_null($url)) {
            throw new CurrencyExchangeURLException();
        }

        $this->exchangeRateURL = $url;
    }
}
