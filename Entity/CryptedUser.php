<?php

namespace Entity;

use Utility\EncryptTrait;
use Entity\User;

class CryptedUser extends User {
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