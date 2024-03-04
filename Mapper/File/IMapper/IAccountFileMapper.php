<?php

namespace Mapper\File\IMapper;

use Entity\IEntity\IAccount;

interface IAccountFileMapper {
    public function getLine(IAccount $account) : string;
    public function getAccount(string $line) : IAccount;
}