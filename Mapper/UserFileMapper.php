<?php

namespace Mapper;

use Entity\User;
use Entity\IEntity\IUser;
use Mapper\IMapper\IUserFileMapper;

class UserFileMapper implements IUserFileMapper {
    public function getLine(IUser $user) :  string {
        return $user->getName() . ';' . $user->getUsername() . ';' . $user->getPassword();
    }   

    public function getUser(string $line) : IUser {
        $row = explode(';', $line);
        
        $user = new User();
        $user->setName($row[0]);
        $user->setUsername($row[1]);
        $user->setPassword($row[2]);

        return $user;
    }
}