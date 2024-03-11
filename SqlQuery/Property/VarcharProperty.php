<?php

namespace SqlQuery\Property;

class VarcharProperty extends Property {
    private string $column;

    public function __construct(string $column) {
        $this->column = $column;
    }

    public function getProperty() : string {
        return $this->column . ' VARCHAR(255)';
    }
}