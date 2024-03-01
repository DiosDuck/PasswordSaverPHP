<?php

namespace Mapper;

use Entity\IEntity\IAccount;
use Entity\CryptedAccount;
use Exception\Type\AccountTypeException;
use Mapper\IMapper\IAccountFileMapper;

class CryptedAccountFileMapper implements IAccountFileMapper {
    public function getLine(IAccount $account) : string {
        if (!$account instanceof CryptedAccount) {
            throw new AccountTypeException();
        }
        return $account->getDomain() . ';' . $account->getUsername() . ';' . $account->getRawPassword() . ';' . bin2hex($account->getKey());
    }
    public function getAccount(string $line) : IAccount {
        $rows = explode(';', $line);

        $account = new CryptedAccount();
        $account->setDomain($rows[0]);
        $account->setUsername($rows[1]);
        $account->setRawPassword($rows[2]);
        $account->setKey(hex2bin($rows[3]));

        return $account;
    }
}