<?php

namespace Mapper\IMapper;

use Entity\IEntity\IAccount;
use Entity\IEntity\IUser;

interface IAccountDBMapper {
    public function getDbParameters() : string;
    public function getDbInsertParameters() : string;
    public function getDbUpdateParameters() : string;
    public function getExecutableParameters(IAccount $account) : array;
    public function getAccount(array $array, IUser $user) : IAccount;
    public function getCreateTableNonKeyParameters() : string;
}