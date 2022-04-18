<?php


namespace App\CommissionFee;


use App\CommissionFee\ClientTypes\BusinessType;
use App\CommissionFee\ClientTypes\PrivateType;
use App\CommissionFee\Operations\AbstractOperation;
use App\CommissionFee\Operations\Deposit;
use App\CommissionFee\Operations\Withdraw;

class Calculator
{
    private array $lines;

    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function handle()
    {
        while ($this->lines != null) {
            $firstRaw = array_shift($this->lines);

            $operation = "App\\CommissionFee\\Operations\\" . ucfirst($firstRaw['operation']);
            $clientType = "App\\CommissionFee\\ClientTypes\\" . ucfirst($firstRaw['client_type']) . "Type";

            $objectClientType = $clientType::getInstance();
            $operationObject = $operation::getInstance($objectClientType);
            dump($operationObject->handle($firstRaw));

            foreach ($this->lines as $key => $raw) {
                if ($firstRaw['user_id'] == $raw['user_id']) {

                    dump($operationObject->handle($raw));

                    unset($this->lines[$key]);
                }
            }

//            dump($operationObject->getAmount());

            $operationObject->__destruct();
        }
    }
}
