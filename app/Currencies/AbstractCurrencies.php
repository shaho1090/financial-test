<?php


namespace App\Currencies;


use App\Exceptions\CurrencyNotFoundException;
use App\Exceptions\CurrencyExchangeURLException;
use Illuminate\Support\Facades\Http;

abstract class AbstractCurrencies
{
    protected array $rates = [];

    protected string $base;

    private string $exchangeRateURL = '';

    public function getBase(): string
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
     * @param string $currency
     * @return bool
     */
    public function checkIfExist(string $currency): bool
    {
        return array_key_exists($currency, $this->getRates());
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
        if (!$this->checkIfExist($currency)) {
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

    public function isBase($currency): bool
    {
        return $this->base == $currency;
    }

    /**
     * @throws CurrencyNotFoundException
     */
    public function calculateAmountFor($currency, $amount)
    {
        return $amount / $this->getRateFor($currency);
    }
}
