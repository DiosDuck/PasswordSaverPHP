<?php

namespace Builder\IBuilder;

use Entity\User;
use Entity\Account;

interface IAccountBuilder {
	public function createAccount() : void;
	public function setDomain(string $domain) : void;
	public function setUsername(string $username) : void;
	public function setPassword(string $password) : void;
	public function setUser(User $user) : void;
	public function getAccount() : Account;
}