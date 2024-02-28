<?php

namespace Builder;

use Entity\CryptedUser;
use Builder\UserBuilder;
use Exception\Type\UserTypeException;

class CryptedUserBuilder extends UserBuilder {
	public function createUser() : void {
		$this->user = new CryptedUser();
	}
	
	public function setRawPassword(string $password) : void {
		if (!$this->user instanceof CryptedUser) {
			throw new UserTypeException();
		}
		$this->user->setRawPassword($password);
	}
	
	public function setKey(string $key) : void {
		if (!$this->user instanceof CryptedUser) {
			throw new UserTypeException();
		}
		$this->user->setKey($key);
	}
}