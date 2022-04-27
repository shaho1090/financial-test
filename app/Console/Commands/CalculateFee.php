<?php

namespace App\Console\Commands;

use App\CommissionFee\UserOperationFactory;
use App\Exceptions\CurrencyNotFoundException;
use App\Exceptions\FileHandlingException;
use App\FileHandling\CSVFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CalculateFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:fee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate fee based on input csv file';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileHandlingException
     * @throws CurrencyNotFoundException
     */
    public function handle()
    {
        $inputFile = Storage::disk('local')->path('data.csv');

        $raws = (new CSVFile($inputFile))->handle()->getArray();

        (new UserOperationFactory($raws))->handle();
    }
}
