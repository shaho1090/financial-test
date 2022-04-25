<?php


namespace App\CommissionFee\Rules;


use App\CommissionFee\UserOperation;

abstract class AbstractRule
{

    protected InterfaceRule $nextRule;
    protected UserOperation $userOperation;

    public function __construct(UserOperation $userOperation)
    {
        $this->userOperation = $userOperation;
    }

    public function setNextRule(InterfaceRule $interfaceRule)
    {
        $this->nextRule = $interfaceRule;
        return $this;
    }

    public function goToTheNextRule()
    {
        if(!empty($this->nextRule)){
           return  $this->nextRule->check();
        }

        return $this->userOperation->getCalculatedFee();
    }
}
