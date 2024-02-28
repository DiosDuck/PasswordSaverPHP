<?php

namespace Service\IService;

use Entity\User;
use Utility\AccountListDTO;

interface IAccountService {
	public function addAccount(User $user, string $domain, string $username, string $password) : void;
	public function deleteAccount(User $user, string $domain) : void;
	public function getAccountsByDomain(User $user, string $domain) : AccountListDTO;
	public function deleteUser(User $user) : void;
	public function updateAccountPassword(User $user, string $domain, string $newPassword) : void;
}