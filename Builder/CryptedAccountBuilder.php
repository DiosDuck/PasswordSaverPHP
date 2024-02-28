<?php

namespace Builder;

use Entity\CryptedAccount;
use Builder\AccountBuilder;
use Exception\Type\AccountTypeException;

class CryptedAccountBuilder extends AccountBuilder {
	public function createAccount() : void {
		$this->account = new CryptedAccount();
	}
	
	public function setRawPassword(string $password) : void {
		if (!$this->account instanceof CryptedAccount) {
			throw new AccountTypeException();
		}
		$this->account->setRawPassword($password);
	}
	
	public function setKey(string $key) : void {
		if (!$this->account instanceof CryptedAccount) {
			throw new AccountTypeException();
		}
		$this->account->setKey($key);
	}
}