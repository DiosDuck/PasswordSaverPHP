<?php

namespace Entity;

class User {
	private string $username;
	private string $password;
	private string $name;
	
	public function getUsername() : string
	{
		return $this->username;
	}
	
	public function getName() : string
	{
		return $this->name;
	}
	
	public function getPassword() : string
	{
		return $this->password;
	}
	
	public function setUsername(string $username) : void
	{
		$this->username = $username;
	}
	
	public function setName(string $name) : void
	{
		$this->name = $name;
	}
	
	public function setPassword(string $password) : void
	{
		$this->password = $password;
	}
}