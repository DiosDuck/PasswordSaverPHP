<?php

namespace Mapper\DB;

use Mapper\DB\IMapper\IUserDBMapper;
use Entity\IEntity\IUser;
use Entity\User;
use SqlQuery\Condition\EqualCondition;
use SqlQuery\Property\PrimaryKeyConstraint;
use SqlQuery\Property\VarcharProperty;
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
            new EqualCondition("username")
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
            new EqualCondition("username")
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
            new EqualCondition("username")
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
                new VarcharProperty("username"),
                new VarcharProperty("name"),
                new VarcharProperty("password")
            ],
            [
                new PrimaryKeyConstraint("username")
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