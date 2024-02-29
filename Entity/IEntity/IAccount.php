<?php

namespace Entity\IEntity;

use Entity\IEntity\IUser;

interface IAccount {
    public function getDomain(): string;
    public function getUsername(): string;
    public function getPassword(): string;
    public function getUser(): IUser;
    public function setDomain(string $domain): void;
    public function setUsername(string $username): void;
    public function setPassword(string $password): void;
    public function setUser(IUser $user): void;
}