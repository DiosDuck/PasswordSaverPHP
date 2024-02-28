<?php

namespace Entity;

use Entity\User;

class TimeoutUser extends User {
	private User $user;
	private int $time;

	public function __construct(User $user) {
		$this->user = $user;
		$this->time = time();
	}
	
	public function getUsername() : string
	{
		return $this->user->getUsername();
	}
	
	public function getName() : string
	{
		return $this->user->getName();
	}
	
	public function getPassword() : string
	{
		return $this->user->getPassword();
	}
	
	public function setUsername(string $username) : void
	{
		$this->user->setUsername($username);
	}
	
	public function setName(string $name) : void
	{
		$this->user->setName($name);
	}
	
	public function setPassword(string $password) : void
	{
		$this->user->setPassword($password);
	}
	
	public function getUser() : User {
		return $this->user;
	}
	
	public function setUser(User $user) : void {
		$this->user = $user;
	}
	
	public function getTime() : int {
		return $this->time;
	}
	
	public function updateTime() : void {
		$this->time = time();
	}
}