<?php

namespace App\Faucet;

class CoinFormat
{
    private $value;
    
    /**
     * CoinFormat constructor.
     * @param $value
     */
    function __construct($value)
    {
        $this->value = $value;
    }
    
    /**
     * @return mixed
     */
    function __toString()
    {
        if ($this->value > 1000000){
            return $this->frauds();
        } elseif ($this->value > 1000){
            return $this->microFrauds();
        } else {
            return $this->nanoFrauds();
        }
    }
    
    private function frauds()
    {
        return ($this->value/1000000).'Fs';
    }
    
    private function microFrauds()
    {
        return ($this->value / 1000) . 'mFs';
    }
    
    private function nanoFrauds()
    {
        return $this->value.'nFs';
    }
}