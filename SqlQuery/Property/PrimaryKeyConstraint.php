<?php

namespace SqlQuery\Property;

use SqlQuery\Property\Constraint;

class PrimaryKeyConstraint extends Constraint {
    private array $columns;

    public function __construct(... $array) {
        $this->columns = $array;
    }
    public function getConstraint() : string {
        return 'PRIMARY KEY (' . implode(", ", $this->columns) . ')';
    }
}