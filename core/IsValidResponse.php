<?php


namespace app\core;


class IsValidResponse
{

    private $isValid = false;
    private $betAmount = 0;

    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
    }

    public function setBetAmount($amount)
    {
        $this->betAmount = $amount;
    }

    public function getIsValid()
    {
        return $this->isValid;
    }

    public function getBetAmount()
    {
        return $this->betAmount;
    }
}