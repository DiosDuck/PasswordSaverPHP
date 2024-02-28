<?php

namespace Builder;

use Entity\User;
use Builder\IBuilder\IUserBuilder;

class UserBuilder implements IUserBuilder {
	protected User $user;
	
	public function createUser() : void {
		$this->user = new User();
	}
	
	public function setUsername(string $username) : void {
		$this->user->setUsername($username);
	}
	
	public function setPassword(string $password) : void {
		$this->user->setPassword($password);
	}
	
	public function setName(string $name) : void {
		$this->user->setName($name);
	}
	public function getUser() : User {
		return $this->user;
	}
}