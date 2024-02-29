<?php
namespace Service\IService;

use Entity\IEntity\IUser;

interface IUserService{
	public function createNewUser(string $name, string $username, string $password) : void;
	public function logIn(string $username, string $password) : IUser;
	public function logOut() : void;
	public function changePassword(IUser $user, string $oldPassword, string $newPassword) : IUser;
	public function changeName(IUser $user, string $password, string $newName) : IUser;
	public function deleteUser(IUser $user, string $password) : void;
}