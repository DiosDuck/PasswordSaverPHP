<?php

namespace Service;

use Entity\User;
use Entity\TimeoutUser;
use Entity\Account;
use Utility\AccountListDTO;
use Repository\IRepository\IAccountRepository;
use Service\AccountService;
use Utility\TimeoutCheckTrait;

class TimeoutAccountService extends AccountService {

	use TimeoutCheckTrait;
	
	public function __construct(IAccountRepository $accountRepository) {
		parent::__construct($accountRepository);
	}
	
	public function addAccount(User $user, string $domain, string $username, string $password) : void {
		$this->checkUserValid($user);
		parent::addAccount($user, $domain, $username, $password);
		$this->updateUser($user);
	}
	public function deleteAccount(User $user, string $domain) : void {
		$this->checkUserValid($user);
		parent::deleteAccount($user, $domain);
		$this->updateUser($user);
	}
	public function getAccountsByDomain(User $user, string $domain) : AccountListDTO {
		$this->checkUserValid($user);
		$accounts = parent::getAccountsByDomain($user, $domain);
		$this->updateUser($user);
		return $accounts;
	}
	
	public function updateAccountPassword(User $user, string $domain, string $newPassword) : void {
		$this->checkUserValid($user);
		parent::updateAccountPassword($user, $domain, $newPassword);
		$this->updateUser($user);
	}
	
	public function deleteUser(User $user) : void {
		$this->checkUserValid($user);
		parent::deleteUser($user);
		$this->updateUser($user);
	}
	
}