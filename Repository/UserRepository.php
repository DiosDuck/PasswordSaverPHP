<?php

namespace Repository;

use Entity\User;
use Repository\IRepository\IUserRepository;
use Exception\Authentification\NotFoundException;
use Exception\Authentification\WrongPasswordException;
use Exception\Authentification\AlreadyExistsException;
use Builder\IBuilder\IUserBuilder;
use Builder\UserBuilder;

class UserRepository implements IUserRepository {
	protected array $users;
	private IUserBuilder $userBuilder = new UserBuilder();
	
	public function __construct() {
		$this->users = [];
	}
	
	public function getAll() : array {
		return $this->users;
	}
	
	public function get(string $username, string $password) : User {
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
	
	public function add(User $newUser) : User {
		foreach ($this->users as $user) {
			if ($user->getUsername() == $newUser->getUsername()) {
				throw new AlreadyExistsException();
			}
		}
		$this->users[] = $newUser;
		return $newUser;
	}
	
	public function delete(string $username, string $password) : User {
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
	
	public function update(User $oldUser, User $newUser) : User {
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
	
	public function getBuilder() : IUserBuilder {
		return $this->userBuilder;
	}
}