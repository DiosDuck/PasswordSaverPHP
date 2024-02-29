<?php

namespace Mapper\IMapper;

use Entity\IEntity\IUser;

interface IUserDBMapper {
    public function getDbParameters() : string;
    public function getDbInsertParameters() : string;
    public function getDbUpdateParameters() : string;
    public function getExecutableParameters(IUser $user) : array;
    public function getUser(array $array) : IUser;
    public function getCreateTableNonKeyParameters() : string;
}