<?php

namespace Mapper\File;

use Entity\IEntity\IAccount;
use Entity\Account;
use Mapper\File\IMapper\IAccountFileMapper;

class AccountFileMapper implements IAccountFileMapper {
    public function getLine(IAccount $account) : string {
        return $account->getDomain() . ';' . $account->getUsername() . ';' . $account->getPassword();
    }
    public function getAccount(string $line) : IAccount {
        $rows = explode(';', $line);

        $account = new Account();
        $account->setDomain($rows[0]);
        $account->setUsername($rows[1]);
        $account->setPassword($rows[2]);

        return $account;
    }
}