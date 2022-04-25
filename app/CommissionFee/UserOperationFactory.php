<?php


namespace App\CommissionFee;


use App\CommissionFee\ClientTypes\BusinessType;
use App\CommissionFee\ClientTypes\PrivateType;
use App\CommissionFee\Operations\AbstractOperation;
use App\CommissionFee\Operations\Deposit;
use App\CommissionFee\Operations\Withdraw;
use App\Exceptions\CurrencyNotFoundException;
use Illuminate\Support\Facades\Storage;

class UserOperationFactory
{
    private array $lines;

    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    /**
     * @throws CurrencyNotFoundException
     */
    public function handle()
    {
        $class_names = [];

        Storage::delete('output.txt');

        foreach($this->lines as $line){
            $class_name = 'user_' . $line['user_id'] . '_' . $line['operation'];

            if (!array_key_exists($class_name, $class_names)) {
                $class_names[$class_name] = new UserOperation();
            }

            Storage::append('output.txt', $class_names[$class_name]->handle($line));
        }
    }
}
