<?php

namespace Tests\Feature;


use App\CommissionFee\UserOperationFactory;
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

        (new UserOperationFactory($raws))->handle();
    }
}
