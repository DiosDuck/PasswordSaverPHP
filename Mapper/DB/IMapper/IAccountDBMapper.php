<?php

namespace Mapper\DB\IMapper;

use Entity\IEntity\IAccount;
use Entity\IEntity\IUser;

interface IAccountDBMapper {
    public function getInsertQuery() : string;
    public function getInsertParameters(IAccount $account) : array;
    public function getOneSelectQuery() : string;
    public function getOneSelectParameters(IUser $user, string $domain) : array;
    public function getDeleteQuery() : string;
    public function getDeleteParameters(IUser $user, string $username) : array;
    public function getDeleteByUserQuery() : string;
    public function getDeleteByUserParameters(IUser $user) : array;
    public function getSelectQuery() : string;
    public function getSelectParameters(IUser $user) : array;
    public function getSelectMultipleDomainQuery() : string;
    public function getSelectMultipleDomainParameters(IUser $user, string $domain) : array;
    public function getUpdateQuery() : string;
    public function getUpdateParameters(IAccount $account) : array;
    public function getCreateTableQuery() : string;
    public function getAccount(array $data) : IAccount;
}