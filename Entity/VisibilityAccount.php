<?php

namespace Entity;

use Entity\IEntity\IAccount;
use Entity\IEntity\IUser;

class VisibilityAccount implements IAccount {
    private IAccount $account;
    private bool $visibility;

    public function __construct(IAccount $account) {
        $this->account = $account;
        $this->visibility = false;
    }

    public function getDomain() : string {
        return $this->account->getDomain();
    }

    public function getUsername() : string {
        return $this->account->getUsername();
    }

    public function getPassword() : string {
        return $this->visibility ? $this->account->getPassword() : "";
    }

    public function getUser() : IUser {
        return $this->account->getUser();
    }

    public function setDomain(string $domain) : void {
        $this->account->setDomain($domain);
    }

    public function setUsername(string $username) : void {
        $this->account->setUsername($username);
    }

    public function setPassword(string $password) : void {
        $this->account->setPassword($password);
    }

    public function setUser(IUser $user) : void {
        $this->account->setUser($user);
    }

    public function setVisibility(bool $visibility) : void {
        $this->visibility = $visibility;
    }
}