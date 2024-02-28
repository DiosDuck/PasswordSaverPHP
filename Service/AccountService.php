<?php

namespace Service;

use Entity\User;
use Entity\Account;
use Utility\AccountListDTO;
use Repository\IRepository\IAccountRepository;
use Service\IService\IAccountService;
use Builder\IBuilder\IAccountBuilder;

class AccountService implements IAccountService {
	private IAccountRepository $accountRepository;
	private IAccountBuilder $accountBuilder;
	
	public function __construct(IAccountRepository $accountRepository) {
		$this->accountRepository = $accountRepository;
		$this->accountBuilder = $accountRepository->getBuilder();
	}
	
	public function addAccount(User $user, string $domain, string $username, string $password) : void {
		$this->accountBuilder->createAccount();
		$this->accountBuilder->setUser($user);
		$this->accountBuilder->setDomain($domain);
		$this->accountBuilder->setUsername($username);
		$this->accountBuilder->setPassword($password);
		$account = $this->accountBuilder->getAccount();
		
		$this->accountRepository->add($account);
	}
	public function deleteAccount(User $user, string $domain) : void {
		$this->accountRepository->delete($user, $domain);
	}
	public function getAccountsByDomain(User $user, string $domain) : AccountListDTO {
		$acc = $this->accountRepository->getAllByDomain($user, $domain);
		return new AccountListDTO($acc);
	}
	
	public function deleteUser(User $user) : void {
		$this->accountRepository->deleteAll($user);
	}
	
	public function updateAccountPassword(User $user, string $domain, string $newPassword) : void {
		$this->accountBuilder->createAccount();
		$this->accountBuilder->setUser($user);
		$this->accountBuilder->setDomain($domain);
		$this->accountBuilder->setPassword($newPassword);
		$account = $this->accountBuilder->getAccount();
		
		$this->accountRepository->update($account, $account);
	}
}