<?php

namespace SqlQuery\Condition;

use SqlQuery\Condition\ICondition\ICondition;

class LikeCondition implements ICondition{
    private string $parameter;

    public function __construct(string $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getCondition(): string
    {
        return $this->parameter . ' LIKE %' . $this->parameter . '%';
    }
}