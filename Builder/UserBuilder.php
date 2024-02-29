<?php

namespace Builder;

use Entity\IEntity\IUser;
use Entity\User;
use Builder\IBuilder\IUserBuilder;

class UserBuilder implements IUserBuilder {
	protected IUser $user;
	
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
	public function getUser() : IUser {
		return $this->user;
	}
}