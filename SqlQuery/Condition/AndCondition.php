<?php

namespace SqlQuery\Condition;

use SqlQuery\Condition\ICondition\ICondition;

class AndCondition implements ICondition {
    private array $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function getCondition(): string
    {
        return implode(' AND ', array_map(function($condition){return $condition->getCondition();}, $this->array));
    }
}