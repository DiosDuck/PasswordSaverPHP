<?php

namespace Mapper\DB;

use Entity\IEntity\IUser;
use Entity\CryptedUser;
use Exception\Type\UserTypeException;
use Mapper\DB\UserDBMapper;
use SqlQuery\Condition\EqualCondition;
use SqlQuery\Property\Property;
use SqlQuery\Property\PrimaryKeyProperty;

class CryptedUserDBMapper extends UserDBMapper {
    public function getInsertQuery() : string {
        return $this->sqlQuery->getInsertQuery(
            "user",
            ["username", "name", "password", "key"]
        );
    }
    public function getInsertParameters(IUser $user) : array {
        if (!$user instanceof CryptedUser) {
            throw new UserTypeException();
        }
        return [
            "username" => $user->getUsername(),
            "name" => $user->getName(),
            "password" => $user->getRawPassword(),
            "key" => $user->getKey()
        ];
    }

    public function getOneSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "user",
            ["username", "name", "password", "key"],
            new EqualCondition("username")
        );
    }

    public function getSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "user",
            ["username", "name", "password", "key"]
        );
    }
    public function getUpdateQuery() : string {
        return $this->sqlQuery->getUpdateQuery(
            "user",
            ["name", "password", "key"],
            new EqualCondition("username")
        );
    }
    public function getUpdateParameters(IUser $user) : array {
        if (!$user instanceof CryptedUser) {
            throw new UserTypeException();
        }
        return [
            "username" => $user->getUsername(),
            "name" => $user->getName(),
            "password" => $user->getRawPassword(),
            "key" => $user->getKey()
        ];
    }
    public function getCreateTableQuery() : string {
        return $this->sqlQuery->getCreateQuery(
            "user",
            [
                new PrimaryKeyProperty("username", "VARCHAR(255)"),
                new Property("name", "VARCHAR(255)"),
                new Property("password", "VARCHAR(255)"),
                new Property("key", "VARCHAR(255)")
            ]
        );
    }
    public function getUser(array $data) : IUser {
        $user = new CryptedUser();

        $user->setUsername($data["username"]);
        $user->setName($data["name"]);
        $user->setRawPassword($data["password"]);
        $user->setKey($data["key"]);
        
        return $user;
    }
}