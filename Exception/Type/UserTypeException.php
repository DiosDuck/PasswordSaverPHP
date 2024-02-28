<?php

namespace Exception\Type;

use Exception\Type\TypeException;

class UserTypeException extends TypeException {
    public function __toString() : string {
        return parent::__toString() . "Wrong User type";
    }
}