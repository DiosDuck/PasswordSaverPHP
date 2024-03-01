<?php

namespace Mapper;

use Entity\IEntity\IAccount;
use Entity\CryptedAccount;
use Entity\IEntity\IUser;
use Exception\Type\AccountTypeException;
use Mapper\AccountDBMapper;

class CryptedAccountDBMapper extends AccountDBMapper {
    public function getDbParameters() : string {
        return parent::getDbParameters() . ', key';
    }
    public function getDbInsertParameters() : string {
        return parent::getDbInsertParameters() . ', :key';
    }
    public function getDbUpdateParameters() : string {
        return parent::getDbUpdateParameters() . ', key = :key';
    }
    public function getExecutableParameters(IAccount $account) : array {
        if (!$account instanceof CryptedAccount) {
            throw new AccountTypeException();
        }
        return [
            'domain' => $account->getDomain(),
            'username' => $account->getUsername(),
            'password' => $account->getRawPassword(),
            'key' => $account->getKey(),
            'user' => $account->getUser()->getUsername()
        ];
    }
    public function getAccount(array $array, IUser $user) : IAccount {
        $account = new CryptedAccount();

        $account->setDomain($array['domain']);
        $account->setUsername($array['username']);
        $account->setRawPassword($array['password']);
        $account->setKey($array['key']);
        $account->setUser($user);

        return $account;
    }
    public function getCreateTableNonKeyParameters() : string {
        return parent::getCreateTableNonKeyParameters() . ',key varchar(255)';
    }
}