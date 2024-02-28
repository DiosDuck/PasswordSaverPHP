<?php

namespace Service;

use Service\UserService;
use Utility\TimeoutCheckTrait;
use Entity\User;
use Entity\TimeoutUser;
use Repository\IRepository\IUserRepository;

class TimeoutUserService extends UserService {
	use TimeoutCheckTrait;
	
	public function __construct(IUserRepository $userRepository) {
		parent::__construct($userRepository);
	}
	
	public function logIn(string $username, string $password) : User {
		$user = parent::logIn($username, $password);
		$timeoutUser = new TimeoutUser($user);
		return $timeoutUser;
	}
	
	public function changePassword(User $user, string $oldPassword, string $newPassword) : User {
		$timeoutUser = $this->checkUserValid($user);
		$user = parent::changePassword($timeoutUser->getUser(), $oldPassword, $newPassword);
		
		$timeoutUser->setUser($user);
		$this->updateUser($timeoutUser);
		return $timeoutUser;
	}
	
	public function changeName(User $user, string $password, string $newName) : User {
		$timeoutUser = $this->checkUserValid($user);
		$user = parent::changeName($timeoutUser->getUser(), $password, $newName);
		
		$timeoutUser->setUser($user);
		$this->updateUser($timeoutUser);
		return $timeoutUser;
	}
	
	public function deleteUser(User $user, string $password) : void {
		$timeoutUser = $this->checkUserValid($user);
		parent::deleteUser($timeoutUser->getUser(), $password);
	}
}