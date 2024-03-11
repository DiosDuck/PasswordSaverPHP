<?php

namespace SqlQuery\Property;

abstract class Constraint {
    public abstract function getConstraint() : string;
}