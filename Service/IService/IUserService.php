<?php
namespace Service\IService;

use Entity\User;

interface IUserService{
	public function createNewUser(string $name, string $username, string $password) : void;
	public function logIn(string $username, string $password) : User;
	public function logOut() : void;
	public function changePassword(User $user, string $oldPassword, string $newPassword) : User;
	public function changeName(User $user, string $password, string $newName) : User;
	public function deleteUser(User $user, string $password) : void;
}