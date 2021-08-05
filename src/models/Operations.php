<?php

namespace Elitelib;

use DivisionByZeroError;
use InvalidArgumentException;

class Operations{

    public function __construct()
    {
        
    }

    public function add($num1, $num2) {
        if($num1 === null ||$num2 === null
        || !is_numeric($num1) || !is_numeric($num2)) throw new InvalidArgumentException("values are not numneric");
        return $num1 + $num2;
    }

    public function subtract($num1, $num2) {
        if($num1 == null ||$num2 == null
        || !is_numeric($num1) || !is_numeric($num2)) throw new InvalidArgumentException("values are not numneric");
        return $num1 - $num2;
    }

    public function multiply($num1, $num2) {
        if($num1 == null ||$num2 == null
        || !is_numeric($num1) || !is_numeric($num2)) throw new InvalidArgumentException("values are not numneric");
        return $num1 * $num2;
    }

    public function divide($num1, $num2) {
        if($num1 === null ||$num2 === null
        || !is_numeric($num1) || !is_numeric($num2)) throw new InvalidArgumentException("values are not numneric");
        if($num2 === 0) throw new DivisionByZeroError();
        return $num1 / $num2;
    }

}