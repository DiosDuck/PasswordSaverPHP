<?php

namespace Mapper\DB\IMapper;

use Entity\IEntity\IUser;

interface IUserDBMapper {
    public function getInsertQuery() : string;
    public function getInsertParameters(IUser $user) : array;
    public function getOneSelectQuery() : string;
    public function getOneSelectParameters(string $username) : array;
    public function getDeleteQuery() : string;
    public function getDeleteParameters(string $username) : array;
    public function getSelectQuery() : string;
    public function getUpdateQuery() : string;
    public function getUpdateParameters(IUser $user) : array;
    public function getCreateTableQuery() : string;
    public function getUser(array $data) : IUser;
}