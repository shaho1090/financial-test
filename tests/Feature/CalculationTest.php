<?php

namespace Tests\Feature;


use App\CommissionFee\Calculator;
use App\CommissionFee\ClientTypes\BusinessType;
use App\CommissionFee\ClientTypes\PrivateType;
use App\CommissionFee\Operations\Deposit;
use App\CommissionFee\Operations\Withdraw;
use App\Exceptions\FileHandlingException;
use App\FileHandling\CSVFIle;
use App\Currencies\Currencies;
use App\Exceptions\CurrencyExchangeURLException;
use App\Exceptions\CurrencyNotFoundException;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CalculationTest extends TestCase
{
    /**
     * @throws CurrencyExchangeURLException
     * @throws CurrencyNotFoundException
     * @throws FileHandlingException
     */
    public function test_calculation_data()
    {

        $inputFile = Storage::disk('local')->path('data.csv');

        $raws = (new CSVFile($inputFile))->handle()->getArray();

        (new Calculator($raws))->handle();

        dd();

        dump($response);
        dd();

        dd($data = fgetcsv($inputFile, 1000, ","));

        $currencies = (new Currencies())->setOnlineRates();

        dd($currencies->getRateFor('USD'));
        dd($currencies->getRateFor('SCR'));
        dd($currencies->getRateFor('SCR'));
        dump((new Currencies())->getAll());
        dump((new Currencies())->getBase());
        dd((new Currencies())->getRates());

        dd($inputFile);

    }

    public function setUserIdAsKey($array)
    {
        $data = [

        ];

        array_filter($array, array_walk($array, function ($value, $key) {
            if ($key == 'user_id') {
                return $value;
            }
        }));
    }


}
