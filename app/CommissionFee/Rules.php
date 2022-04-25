<?php


namespace App\CommissionFee;


use App\CommissionFee\Rules\AbstractRule;
use App\CommissionFee\Rules\MaxAmount;
use App\CommissionFee\Rules\MaxNumber;

class Rules
{

    private UserOperation $userOperation;

    public function __construct(UserOperation $userOperation)
    {
        $this->userOperation = $userOperation;
    }

    public function chain(): float
    {
        $MaxAmount = new MaxAmount($this->userOperation);
        $MaxNumber = new MaxNumber($this->userOperation);

        $MaxAmount->setNextRule($MaxNumber)->check();

        $this->userOperation->setPreviousLine();

        return $this->userOperation->getCalculatedFee();
    }
}
