<?php


namespace App\CommissionFee\ClientTypes;


use App\CommissionFee\Singleton;

abstract class AbstractClientType extends Singleton
{
    public function getClassName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
