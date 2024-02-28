<?php

namespace Exception\Type;

use Exception\Type\TypeException;

class AccountTypeException extends TypeException {
    public function __toString() : string {
        return parent::__toString() . "Wrong Account type";
    }
}