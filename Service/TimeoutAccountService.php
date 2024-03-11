<?php

namespace Service;

use Builder\IBuilder\IAccountBuilder;
use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;
use Utility\AccountListDTO;
use Repository\IRepository\IAccountRepository;
use Service\AccountService;
use Utility\TimeoutCheckTrait;

class TimeoutAccountService extends AccountService {

	use TimeoutCheckTrait;
	
	public function __construct(IAccountRepository $accountRepository, IAccountBuilder $accountBuilder) {
		parent::__construct($accountRepository, $accountBuilder);
	}
	
	public function addAccount(IUser $user, string $domain, string $username, string $password) : void {
		$this->checkUserValid($user);
		$this->updateUser($user);
		parent::addAccount($user, $domain, $username, $password);
	}
	public function deleteAccount(IUser $user, string $domain) : void {
		$this->checkUserValid($user);
		$this->updateUser($user);
		parent::deleteAccount($user, $domain);
	}
	public function getAccountsByDomain(IUser $user, string $domain) : AccountListDTO {
		$this->checkUserValid($user);
		$this->updateUser($user);
		return parent::getAccountsByDomain($user, $domain);
	}
	
	public function updateAccountPassword(IUser $user, string $domain, string $newPassword) : void {
		$this->checkUserValid($user);
		$this->updateUser($user);
		parent::updateAccountPassword($user, $domain, $newPassword);
	}
	
	public function deleteUser(IUser $user) : void {
		$this->checkUserValid($user);
		$this->updateUser($user);
		parent::deleteUser($user);
	}

	
	public function updateAccountUsername(IUser $user, string $domain, string $newUsername) : void {
		$this->checkUserValid($user);
		$this->updateUser($user);
		parent::updateAccountUsername($user, $domain, $newUsername);
	}
}