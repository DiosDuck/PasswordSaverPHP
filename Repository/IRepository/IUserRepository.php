<?php

namespace Repository\IRepository;

use Entity\IEntity\IUser;
use Builder\IBuilder\IUserBuilder;

interface IUserRepository {
	public function getAll() : array;
	public function get(string $username, string $password) : IUser;
	public function add(IUser $newUser) : IUser;
	public function delete(string $username, string $password) : IUser;
	public function update(IUser $oldUser, IUser $newUser) : IUser;
}