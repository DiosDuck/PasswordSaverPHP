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
		parent::addAccount($user, $domain, $username, $password);
		$this->updateUser($user);
	}
	public function deleteAccount(IUser $user, string $domain) : void {
		$this->checkUserValid($user);
		parent::deleteAccount($user, $domain);
		$this->updateUser($user);
	}
	public function getAccountsByDomain(IUser $user, string $domain) : AccountListDTO {
		$this->checkUserValid($user);
		$accounts = parent::getAccountsByDomain($user, $domain);
		$this->updateUser($user);
		return $accounts;
	}
	
	public function updateAccountPassword(IUser $user, string $domain, string $newPassword) : void {
		$this->checkUserValid($user);
		parent::updateAccountPassword($user, $domain, $newPassword);
		$this->updateUser($user);
	}
	
	public function deleteUser(IUser $user) : void {
		$this->checkUserValid($user);
		parent::deleteUser($user);
		$this->updateUser($user);
	}
	
}