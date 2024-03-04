<?php

namespace SqlQuery\Property;

class PrimaryKeyProperty extends Property {
    public function getProperty() : string {
        return parent::getProperty() . ' Primary key';
    }
}