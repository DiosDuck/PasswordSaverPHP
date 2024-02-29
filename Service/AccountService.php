<?php

namespace Service;

use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;
use Utility\AccountListDTO;
use Repository\IRepository\IAccountRepository;
use Service\IService\IAccountService;
use Builder\IBuilder\IAccountBuilder;

class AccountService implements IAccountService {
	private IAccountRepository $accountRepository;
	private IAccountBuilder $accountBuilder;
	
	public function __construct(IAccountRepository $accountRepository, IAccountBuilder $accountBuilder) {
		$this->accountRepository = $accountRepository;
		$this->accountBuilder = $accountBuilder;
	}
	
	public function addAccount(IUser $user, string $domain, string $username, string $password) : void {
		$this->accountBuilder->createAccount();
		$this->accountBuilder->setUser($user);
		$this->accountBuilder->setDomain($domain);
		$this->accountBuilder->setUsername($username);
		$this->accountBuilder->setPassword($password);
		$account = $this->accountBuilder->getAccount();
		
		$this->accountRepository->add($account);
	}
	public function deleteAccount(IUser $user, string $domain) : void {
		$this->accountRepository->delete($user, $domain);
	}
	public function getAccountsByDomain(IUser $user, string $domain) : AccountListDTO {
		$acc = $this->accountRepository->getAllByDomain($user, $domain);
		return new AccountListDTO($acc);
	}
	
	public function deleteUser(IUser $user) : void {
		$this->accountRepository->deleteAll($user);
	}
	
	public function updateAccountPassword(IUser $user, string $domain, string $newPassword) : void {
		$account = $this->accountRepository->get($user, $domain);
		$account->setPassword($newPassword);
		
		$this->accountRepository->update($account, $account);
	}
}