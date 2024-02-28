<?php

namespace Repository\IRepository;

use Entity\User;
use Builder\IBuilder\IUserBuilder;

interface IUserRepository {
	public function getAll() : array;
	public function get(string $username, string $password) : User;
	public function add(User $newUser) : User;
	public function delete(string $username, string $password) : User;
	public function update(User $oldUser, User $newUser) : User;
	public function getBuilder() : IUserBuilder;
}