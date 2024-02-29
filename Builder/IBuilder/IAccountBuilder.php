<?php

namespace Builder\IBuilder;

use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;

interface IAccountBuilder {
	public function createAccount() : void;
	public function setDomain(string $domain) : void;
	public function setUsername(string $username) : void;
	public function setPassword(string $password) : void;
	public function setUser(IUser $user) : void;
	public function getAccount() : IAccount;
}