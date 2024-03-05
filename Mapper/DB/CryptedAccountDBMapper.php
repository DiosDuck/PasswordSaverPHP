<?php

namespace Mapper\DB;

use Entity\IEntity\IAccount;
use Entity\CryptedAccount;
use Entity\IEntity\IUser;
use Exception\Type\AccountTypeException;
use Mapper\DB\AccountDBMapper;
use SqlQuery\Condition\AndCondition;
use SqlQuery\Condition\EqualCondition;
use SqlQuery\Condition\LikeCondition;
use SqlQuery\Property\Property;
use SqlQuery\Property\PrimaryKeyProperty;
use SqlQuery\Property\ForeignKeyConstraint;

class CryptedAccountDBMapper extends AccountDBMapper {

    public function getInsertQuery() : string {
        return $this->sqlQuery->getInsertQuery(
            "account",
            ["domain", "username", "password", "key", "user"]
        );
    }
    public function getInsertParameters(IAccount $account) : array {
        if (!$account instanceof CryptedAccount) {
            throw new AccountTypeException();
        }
        return [
            "domain" => $account->getDomain(),
            "username" => $account->getUsername(),
            "password" => $account->getRawPassword(),
            "key" => $account->getKey(),
            "user" => $account->getUser()->getUsername()
        ];
    }
    public function getOneSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password", "key"],
            new AndCondition([new EqualCondition("domain"), new EqualCondition("user")])
        );
    }
    public function getSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password", "key"],
            new EqualCondition("user")
        );
    }
    public function getSelectMultipleDomainQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password", "key"],
            new AndCondition([new LikeCondition("domain"), new EqualCondition("user")])
        );
    }
    public function getUpdateQuery() : string {
        return $this->sqlQuery->getUpdateQuery(
            "account",
            ["password", "key", "username"],
            new AndCondition([new EqualCondition("user"), new EqualCondition("domain")])
        );
    }
    public function getUpdateParameters(IAccount $account) : array {
        if (!$account instanceof CryptedAccount) {
            throw new AccountTypeException();
        }
        return [
            "domain" => $account->getDomain(),
            "username" => $account->getUsername(),
            "password" => $account->getRawPassword(),
            "key" => $account->getKey(),
            "user" => $account->getUser()->getUsername()
        ];
    }
    public function getCreateTableQuery() : string {
        return $this->sqlQuery->getCreateQuery(
            "account",
            [
                new PrimaryKeyProperty("domain", "VARCHAR(255)"),
                new Property("username", "VARCHAR(255)"),
                new Property("password", "VARCHAR(255)"),
                new Property("key", "VARCHAR(255)"),
                new Property("user", "VARCHAR(255)")
            ],
            new ForeignKeyConstraint("user", "user", "username")
        );
    }
    public function getAccount(array $data, IUser $user) : IAccount {
        $account = new CryptedAccount();

        $account->setDomain($data["domain"]);
        $account->setRawPassword($data["password"]);
        $account->setKey($data["key"]);
        $account->setUsername($data["username"]);
        $account->setUser($user);

        return $account;
    }
}