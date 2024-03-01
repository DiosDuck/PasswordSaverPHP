<?php

namespace Mapper;

use Entity\IEntity\IUser;
use Entity\CryptedUser;
use Exception\Type\UserTypeException;
use Mapper\UserDBMapper;

class CryptedUserDBMapper extends UserDBMapper {
    public function getDbParameters() : string {
        return parent::getDbParameters() . ', key';
    }
    public function getDbInsertParameters() : string {
        return parent::getDbInsertParameters() . ', :key';
    }
    public function getDbUpdateParameters() : string {
        return parent::getDbUpdateParameters() . ', key = :key';
    }
    public function getExecutableParameters(IUser $user) : array {
        if (!$user instanceof CryptedUser) {
            throw new UserTypeException();
        }
        return [
            'name' => $user->getName(),
            'username' => $user->getUsername(),
            'password' => $user->getRawPassword(),
            'key' => $user->getKey()
        ];
    }
    public function getUser(array $array) : IUser {
        $user = new CryptedUser();

        $user->setName($array['name']);
        $user->setUsername($array['username']);
        $user->setRawPassword($array['password']);
        $user->setKey($array['key']);

        return $user;
    }
    public function getCreateTableNonKeyParameters() : string {
        return parent::getCreateTableNonKeyParameters() . ',key varchar(255)';
    }
}