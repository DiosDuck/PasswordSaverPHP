<?php

namespace Mapper\File;

use Entity\CryptedUser;
use Entity\IEntity\IUser;
use Exception\Type\UserTypeException;
use Mapper\File\IMapper\IUserFileMapper;

class CryptedUserFileMapper implements IUserFileMapper {
    public function getLine(IUser $user) :  string {
        if (!$user instanceof CryptedUser) {
            throw new UserTypeException();
        }
        return $user->getName() . ';' . $user->getUsername() . ';' . $user->getRawPassword() . ';' . bin2hex($user->getKey());
    }   

    public function getUser(string $line) : IUser {
        $row = explode(';', $line);
        
        $user = new CryptedUser();
        $user->setName($row[0]);
        $user->setUsername($row[1]);
        $user->setRawPassword($row[2]);
        $user->setKey(hex2bin($row[3]));

        return $user;
    }
}