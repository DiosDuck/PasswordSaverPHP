<?php

namespace Service;

use Builder\IBuilder\IUserBuilder;
use Service\UserService;
use Utility\TimeoutCheckTrait;
use Entity\IEntity\IUser;
use Entity\TimeoutUser;
use Repository\IRepository\IUserRepository;

class TimeoutUserService extends UserService {
	use TimeoutCheckTrait;
	
	public function __construct(IUserRepository $userRepository, IUserBuilder $userBuilder) {
		parent::__construct($userRepository, $userBuilder);
	}
	
	public function logIn(string $username, string $password) : IUser {
		$user = parent::logIn($username, $password);
		$timeoutUser = new TimeoutUser($user);
		return $timeoutUser;
	}
	
	public function changePassword(IUser $user, string $oldPassword, string $newPassword) : IUser {
		$timeoutUser = $this->checkUserValid($user);
		$user = parent::changePassword($timeoutUser->getUser(), $oldPassword, $newPassword);
		
		$timeoutUser->setUser($user);
		$this->updateUser($timeoutUser);
		return $timeoutUser;
	}
	
	public function changeName(IUser $user, string $password, string $newName) : IUser {
		$timeoutUser = $this->checkUserValid($user);
		$user = parent::changeName($timeoutUser->getUser(), $password, $newName);
		
		$timeoutUser->setUser($user);
		$this->updateUser($timeoutUser);
		return $timeoutUser;
	}
	
	public function deleteUser(IUser $user, string $password) : void {
		$timeoutUser = $this->checkUserValid($user);
		parent::deleteUser($timeoutUser->getUser(), $password);
	}
}