<?php

namespace Builder\IBuilder;

use Entity\IEntity\IUser;

interface IUserBuilder {
	public function createUser() : void;
	public function setUsername(string $username) : void;
	public function setPassword(string $password) : void;
	public function setName(string $name) : void;
	public function getUser() : IUser;
}