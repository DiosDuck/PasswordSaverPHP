<?php

namespace Builder;

use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;
use Entity\Account;
use Builder\IBuilder\IAccountBuilder;

class AccountBuilder implements IAccountBuilder {
	protected IAccount $account;
	
	public function createAccount() : void {
		$this->account = new Account();
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
	
	public function getAccount() : IAccount {
		return $this->account;
	}
}