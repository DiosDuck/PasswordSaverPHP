<?php

namespace Entity;

use Entity\Account;
use Utility\EncryptTrait;

class CryptedAccount extends Account {
	use EncryptTrait;
	
	public function getRawPassword() : string {
		return parent::getPassword();
	}
	
	public function setRawPassword(string $rawPassword) : void {
		parent::setPassword($rawPassword);
	}
	
	public function getPassword() : string {
		$rawPassword = $this->getRawPassword();
		return $this->decrypt($rawPassword);
	}
	
	public function setPassword(string $password) : void {
		$this->setKey($this->generateKey());
		$rawPassword = $this->encrypt($password);
		$this->setRawPassword($rawPassword);
	}
}