<?php

namespace SqlQuery;

use SqlQuery\Condition\ICondition\ICondition;

class SqlQuery {
    public function getInsertQuery(string $table, array $columns) : string {
        return $this->query(
            [$this->insert($table, $columns), $this->values($columns)]
        );
    }

    public function getSelectQuery(string $table, array $getColumns, ?ICondition $conditon = null) : string {
        $rows = [
            $this->select($getColumns),
            $this->from($table)
        ];
        if ($conditon) {
            $rows[] = $this->where($conditon);
        }
        return $this->query($rows);
    }

    public function getUpdateQuery(string $table, array $setColumns, ICondition $conditon) : string {
        return $this->query(
            [$this->update($table), $this->set($setColumns), $this->where($conditon)]
        );
    }

    public function getDeleteQuery(string $table, ICondition $conditon) : string {
        return $this->query(
            [$this->delete($table), $this->where($conditon)]
        );
    }

    public function getCreateQuery(string $table, array $properties, array $constraints) : string {
        $output = $this->create($table) . '(';
        $output .= implode(",\n", array_merge(
            array_map(fn($property) => $property->getProperty(), $properties),
            array_map(fn($constraint) => $constraint->getConstraint(), $constraints)
        ));
        $output .= ')';
        return $output;
    }

    private function insert(string $table, array $columns) : string {
        return 'Insert into ' . $table  .  '(' . implode(',', $columns) . ')';
    }

    private function values(array $columns) : string {
        return 'values (' . implode(',', array_map(function($column) {return ':' . $column;}, $columns)) . ')';
    }

    private function select(array $columns) : string {
        return 'Select ' . implode(',', $columns);
    }

    private function from(string $table) : string {
        return 'From ' . $table;
    }

    private function where(ICondition $conditon) : string {
        return 'Where ' . $conditon->getCondition();
    }

    private function delete(string $table) : string {
        return 'Delete from ' . $table;
    }

    private function update(string $table) : string {
        return 'Update ' . $table;
    }

    private function set(array $columns) : string {
        return 'Set ' . implode(',', array_map(function ($column) {return $column .  '=:' . $column;}, $columns));
    }

    private function create(string $table) : string {
        return 'CREATE TABLE IF NOT EXISTS ' . $table;
    }

    private function query(array $rows) : string {
        return implode(PHP_EOL, $rows);
    }
}