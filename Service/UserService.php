<?php
namespace Service;

use Service\IService\IUserService;
use Repository\IRepository\IUserRepository;
use Builder\IBuilder\IUserBuilder;
use Entity\IEntity\IUser;

class UserService implements IUserService {
	private IUserRepository $userRepository;
	private IUserBuilder $userBuilder;
	
	public function __construct(IUserRepository $userRepository, IUserBuilder $userBuilder) {
		$this->userRepository = $userRepository;
		$this->userBuilder = $userBuilder;
	}
	
	public function createNewUser(string $name, string $username, string $password) : void {
		$this->userBuilder->createUser();
		$this->userBuilder->setName($name);
		$this->userBuilder->setUsername($username);
		$this->userBuilder->setPassword($password);
		$user = $this->userBuilder->getUser();
		
		$user = $this->userRepository->add($user);
	}
	
	public function logIn(string $username, string $password) : IUser {
		return $this->userRepository->get($username, $password);
	}
	
	public function logOut() : void {
		//at the moment do nothing
	}
	
	public function changePassword(IUser $user, string $oldPassword, string $newPassword) : IUser {
		$oldUser = clone $user;
		$newUser = clone $user;
		
		$oldUser->setPassword($oldPassword);
		$newUser->setPassword($newPassword);
		
		return $this->userRepository->update($oldUser, $newUser);
	}
	
	public function changeName(IUser $user, string $password, string $newName) : IUser {
		$oldUser = clone $user;
		$newUser = clone $user;
		
		$oldUser->setPassword($password);
		$newUser->setPassword($password);
		$newUser->setName($newName);
		
		return $this->userRepository->update($oldUser, $newUser);
	}
	
	public function deleteUser(IUser $user, string $password) : void {
		$this->userRepository->delete($user->getUsername(), $password);
	}
}
