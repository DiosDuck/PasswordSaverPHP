<?php

namespace Mapper\File\IMapper;

use Entity\IEntity\IUser;

interface IUserFileMapper {
    public function getLine(IUser $user): string;
    public function getUser(string $line): IUser;
}