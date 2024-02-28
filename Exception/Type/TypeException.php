<?php

namespace Exception\Type;

use Exception;

class TypeException extends Exception {
    public function __toString() : string {
        return "TypeException: ";
    }
}