<?php

namespace SqlQuery\Condition\ICondition;

interface ICondition {
    public function getCondition(): string;
}