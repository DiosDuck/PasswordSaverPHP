<?php

namespace Mapper\DB;

use Entity\IEntity\IAccount;
use Entity\Account;
use Entity\IEntity\IUser;
use Mapper\DB\IMapper\IAccountDBMapper;
use SqlQuery\Condition\AndCondition;
use SqlQuery\Condition\EqualCondition;
use SqlQuery\Condition\LikeCondition;
use SqlQuery\Property\ForeignKeyConstraint;
use SqlQuery\Property\PrimaryKeyProperty;
use SqlQuery\Property\Property;
use SqlQuery\SqlQuery;

class AccountDBMapper implements IAccountDBMapper {
    protected SqlQuery $sqlQuery;

    public function __construct(SqlQuery $sqlQuery)
    {
        $this->sqlQuery = $sqlQuery;
    }

    public function getInsertQuery() : string {
        return $this->sqlQuery->getInsertQuery(
            "account",
            ["domain", "username", "password", "user"]
        );
    }
    public function getInsertParameters(IAccount $account) : array {
        return [
            "domain" => $account->getDomain(),
            "username" => $account->getUsername(),
            "password" => $account->getPassword(),
            "user" => $account->getUser()->getUsername()
        ];
    }
    public function getOneSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password"],
            new AndCondition([new EqualCondition("domain"), new EqualCondition("user")])
        );
    }
    public function getOneSelectParameters(IUser $user, string $domain) : array {
        return [
            "user" => $user->getUsername(),
            "domain" => $domain
        ];
    }
    public function getDeleteQuery() : string {
        return $this->sqlQuery->getDeleteQuery(
            "account",
            new AndCondition([new EqualCondition("domain"), new EqualCondition("user")])
        );
    }
    public function getDeleteParameters(IUser $user, string $domain) : array {
        return [
            "user" => $user->getUsername(),
            "domain" => $domain
        ];
    }
    
    public function getDeleteByUserQuery() : string {
        return $this->sqlQuery->getDeleteQuery(
            "account",
            new EqualCondition("user")
        );
    }
    public function getDeleteByUserParameters(IUser $user) : array {
        return [
            "user" => $user->getUsername()
        ];
    }
    public function getSelectQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password"],
            new EqualCondition("user")
        );
    }
    public function getSelectParameters(IUser $user) : array {
        return [
            "user" => $user->getUsername()
        ];
    }
    public function getSelectMultipleDomainQuery() : string {
        return $this->sqlQuery->getSelectQuery(
            "account",
            ["domain", "username", "password"],
            new AndCondition([new LikeCondition("domain"), new EqualCondition("user")])
        );
    }
    public function getSelectMultipleDomainParameters(IUser $user, string $domain) : array{
        return [
            "user" => $user->getUsername(),
            "domain" => $domain
        ];
    }
    public function getUpdateQuery() : string {
        return $this->sqlQuery->getUpdateQuery(
            "account",
            ["password", "username"],
            new AndCondition([new EqualCondition("user"), new EqualCondition("domain")])
        );
    }
    public function getUpdateParameters(IAccount $account) : array {
        return [
            "domain" => $account->getDomain(),
            "username" => $account->getUsername(),
            "password" => $account->getPassword(),
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
                new Property("user", "VARCHAR(255)"),
            ],
            new ForeignKeyConstraint("user", "user", "username")
        );
    }
    public function getAccount(array $data) : IAccount {
        $account = new Account();

        $account->setDomain($data["domain"]);
        $account->setPassword($data["password"]);
        $account->setUsername($data["username"]);

        return $account;
    }
}