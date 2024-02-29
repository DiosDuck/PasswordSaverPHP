<?php

namespace Service\IService;

use Entity\IEntity\IUser;
use Utility\AccountListDTO;

interface IAccountService {
	public function addAccount(IUser $user, string $domain, string $username, string $password) : void;
	public function deleteAccount(IUser $user, string $domain) : void;
	public function getAccountsByDomain(IUser $user, string $domain) : AccountListDTO;
	public function deleteUser(IUser $user) : void;
	public function updateAccountPassword(IUser $user, string $domain, string $newPassword) : void;
}