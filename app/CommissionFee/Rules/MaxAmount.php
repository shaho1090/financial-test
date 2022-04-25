<?php


namespace App\CommissionFee\Rules;


class MaxAmount extends AbstractRule implements InterfaceRule
{

    public function check(): float|int
    {
        if(!$this->userOperation->isMaxAmountSet()){
            return $this->goToTheNextRule();
        }

        $this->userOperation->addAmountInDuration();

        if($this->userOperation->isAmountExceeded()){
            return $this->userOperation->calculateFeeBasedOnMaxAmount();
        }

        return $this->goToTheNextRule();
    }
}
