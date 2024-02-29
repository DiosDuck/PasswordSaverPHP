<?php

namespace Mapper;

use Entity\IEntity\IAccount;
use Entity\Account;
use Entity\IEntity\IUser;
use Mapper\IMapper\IAccountDBMapper;

class AccountDBMapper implements IAccountDBMapper {
    public function getDbParameters() : string {
        return 'domain, username, password, user';
    }
    public function getDbInsertParameters() : string {
        return ':domain, :username, :password, :user';
    }
    public function getDbUpdateParameters() : string {
        return 'username = :username, password = :password';
    }
    public function getExecutableParameters(IAccount $account) : array {
        return [
            'domain' => $account->getDomain(),
            'username' => $account->getUsername(),
            'password' => $account->getPassword(),
            'user' => $account->getUser()->getUsername()
        ];
    }
    public function getAccount(array $array, IUser $user) : IAccount {
        $account = new Account();

        $account->setDomain($array['domain']);
        $account->setUsername($array['username']);
        $account->setPassword($array['password']);
        $account->setUser($user);

        return $account;
    }
    public function getCreateTableNonKeyParameters() : string {
        return ',username varchar(255),password varchar(255)';
    }
}