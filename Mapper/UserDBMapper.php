<?php

namespace Mapper;

use Mapper\IMapper\IUserDBMapper;
use Entity\IEntity\IUser;
use Entity\User;
use SqlQuery\Property\PrimaryKeyProperty;
use SqlQuery\Property\Property;
use SqlQuery\SqlQuery;

class UserDBMapper implements IUserDBMapper {
    protected SqlQuery $sqlQuery;

    public function __construct(SqlQuery $sqlQuery) {
        $this->sqlQuery = $sqlQuery;
    }
    
    public function getInsertQuery() : string {
        return $this->sqlQuery->getInsertQuery(
            "user",
            ["username", "name", "password"]
        );
    }
    public function getInsertParameters(IUser $user) : array {
        return [
            "username" => $user->getUsername(),
            "name" => $user->getName(),
            "password" => $user->getPassword()
        ];
    }

    public function getOneSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "user",
            ["username", "name", "password"],
            ["username"]
        );
    }
    public function getOneSelectParameters(string $username) : array {
        return [
            "username" => $username
        ];
    }
    public function getDeleteQuery() : string {
        return $this->sqlQuery->getDeleteQuery(
            "user",
            ["username"]
        );
    }
    public function getDeleteParameters(string $username) : array {
        return [
            "username" => $username
        ];
    }
    public function getSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "user",
            ["username", "name", "password"]
        );
    }
    public function getUpdateQuery() : string {
        return $this->sqlQuery->getUpdateQuery(
            "user",
            ["name", "password"],
            ["username"]
        );
    }
    public function getUpdateParameters(IUser $user) : array {
        return [
            "username" => $user->getUsername(),
            "name" => $user->getName(),
            "password" => $user->getPassword()
        ];
    }
    public function getCreateTableQuery() : string {
        return $this->sqlQuery->getCreateQuery(
            "user",
            [
                new PrimaryKeyProperty("username", "VARCHAR(255)"),
                new Property("name", "VARCHAR(255)"),
                new Property("password", "VARCHAR(255)")
            ]
        );
    }
    public function getUser(array $data) : IUser {
        $user = new User();

        $user->setUsername($data["username"]);
        $user->setName($data["name"]);
        $user->setPassword($data["password"]);

        return $user;
    }
}