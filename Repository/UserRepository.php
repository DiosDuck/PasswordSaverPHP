<?php

namespace Repository;

use Entity\IEntity\IUser;
use Repository\IRepository\IUserRepository;
use Exception\Authentification\NotFoundException;
use Exception\Authentification\WrongPasswordException;
use Exception\Authentification\AlreadyExistsException;

class UserRepository implements IUserRepository {
	protected array $users;

	public function __construct() {
		$this->users = [];
	}
	
	public function getAll() : array {
		return $this->users;
	}
	
	public function get(string $username, string $password) : IUser {
		foreach ($this->users as $user) {
			if ($user->getUsername() == $username) {
				if ($user->getPassword() == $password) {
					return $user;
				}
				throw new WrongPasswordException();
			}
		}
		throw new NotFoundException();
	}
	
	public function add(IUser $newUser) : IUser {
		foreach ($this->users as $user) {
			if ($user->getUsername() == $newUser->getUsername()) {
				throw new AlreadyExistsException();
			}
		}
		$this->users[] = $newUser;
		return $newUser;
	}
	
	public function delete(string $username, string $password) : IUser {
		foreach ($this->users as $key => $user) {
			if ($user->getUsername() == $username) {
				if ($user->getPassword() == $password) {
					unset($this->users[$key]);
					return $user;
				}
				throw new WrongPasswordException();
			}
		}
		throw new NotFoundException();
	}
	
	public function update(IUser $oldUser, IUser $newUser) : IUser {
		foreach ($this->users as $user) {
			if ($user->getUsername() == $oldUser->getUsername()) {
				if ($user->getPassword() == $oldUser->getPassword()) {
					$user->setName($newUser->getName());
					$user->setPassword($newUser->getPassword());
					return $newUser;
				}
				throw new WrongPasswordException();
			}
		}
		throw new NotFoundException();
	}
}