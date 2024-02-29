<?php

namespace Mapper;

use Mapper\IMapper\IUserDBMapper;
use Entity\IEntity\IUser;
use Entity\User;

class UserDBMapper implements IUserDBMapper {
    public function getDbParameters() : string {
        return 'name, username, password';
    }
    public function getDbInsertParameters() : string {
        return ':name, :username, :password';
    }
    public function getDbUpdateParameters() : string {
        return 'name = :name, password = :password';
    }
    public function getExecutableParameters(IUser $user) : array {
        return [
            'name' => $user->getName(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword()
        ];
    }
    public function getUser(array $array) : IUser {
        $user = new User();

        $user->setName($array['name']);
        $user->setUsername($array['username']);
        $user->setPassword($array['password']);

        return $user;
    }
    public function getCreateTableNonKeyParameters() : string {
        return ',name varchar(255),password varchar(255)';
    }
}