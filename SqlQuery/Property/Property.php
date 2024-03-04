<?php

namespace SqlQuery\Property;

class Property {
    private string $column;
    private string $type;

    public function __construct(string $column, string $type) {
        $this->column = $column;
        $this->type = $type;
    }

    public function getProperty() : string {
        return $this->column . ' ' . $this->type;
    }
}