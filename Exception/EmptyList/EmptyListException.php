<?php

namespace Exception\EmptyList;

use Exception;

class EmptyListException extends Exception {
    public function __toString() : string {
        return "EmptyException: the list is empty";
    }
}