<?php


namespace App\CommissionFee\Rules;


class MaxNumber extends AbstractRule implements InterfaceRule
{
    public function check(): float|int
    {
        if(!$this->userOperation->isMaxNumberSet()){
            return $this->goToTheNextRule();
        }

        $this->userOperation->addNumberInDuration();

        if($this->userOperation->isMaxNumberExceeded()){
            return $this->userOperation->calculateFeeBasedOnMaxNumber();
        }

        return $this->goToTheNextRule();
    }
}
