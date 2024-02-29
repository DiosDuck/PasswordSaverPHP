<?php

namespace Mapper\IMapper;

use Entity\IEntity\IUser;

interface IUserFileMapper {
    public function getLine(IUser $user): string;
    public function getUser(string $line): IUser;
}