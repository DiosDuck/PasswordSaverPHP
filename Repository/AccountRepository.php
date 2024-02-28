<?php

namespace Repository;

use Entity\User;
use Entity\Account;
use Exception\Account\NotFoundException;
use Exception\Account\AlreadyExistsException;
use Repository\IRepository\IAccountRepository;
use Builder\IBuilder\IAccountBuilder;
use Builder\AccountBuilder;

class AccountRepository implements IAccountRepository {
	protected array $accounts;
	private IAccountBuilder $accountBuilder = new AccountBuilder();
	
	public function __construct() {
		$this->accounts = [];
	}
	
	public function add(Account $account) : Account {
		$user = $account->getUser()->getUsername();
		if (!isset($this->accounts[$user])) {
			$this->accounts[$user] = [$account];
			return $account;
		}
		foreach ($this->accounts[$user] as $acc) {
			if ($acc->getDomain() == $account->getDomain()) {
				throw new AlreadyExistsException();
			}
		}
		$this->accounts[$user][] = $account;
		return $account;
	}
	
	public function delete(User $user, string $domain) : Account {
		$user = $user->getUsername();
		if (!isset($this->accounts[$user])) {
			throw new NotFoundException();
		}
		foreach ($this->accounts[$user] as $key=>$acc) {
			if ($acc->getDomain() == $domain) {
				unset($this->accounts[$user][$key]);
				$this->accounts[$user] = array_values($this->accounts[$user]);
				return $acc;
			}
		}
		throw new NotFoundException();
	}
	
	public function getAll(User $user) : array {
		$username = $user->getUsername();
		if (!isset($this->accounts[$username])) {
			return [];
		}
		return $this->accounts[$username];
	}
	
	public function getAllByDomain(User $user, string $domain) : array {
		$username = $user->getUsername();
		if (!isset($this->accounts[$username])) {
			return [];
		}
		$acc = [];
		foreach ($this->accounts[$username] as $account) {
			if (str_contains($account->getDomain(), $domain)) {
				$acc[] = $account;
			}
		}
		return $acc;
	}
	
	public function update(Account $oldAccount, Account $newAccount) : Account {
		$user = $oldAccount->getUser()->getUsername();
		if (!isset($this->accounts[$user])) {
			throw new NotFoundException();
		}
		foreach ($this->accounts[$user] as $key=>$acc) {
			if ($acc->getDomain() == $oldAccount->getDomain()) {
				$acc->setPassword($newAccount->getPassword());
				return $acc;
			}
		}
		throw new NotFoundException();
	}
	
	public function get(User $user, string $domain) : Account {
		$user = $user->getUsername();
		if (!isset($this->accounts[$user])) {
			throw new NotFoundException();
		}
		foreach ($this->accounts[$user] as $key=>$acc) {
			if ($acc->getDomain() == $domain) {
				return $acc;
			}
		}
		throw new NotFoundException();
	}
	
	public function deleteAll(User $user) : void {
		$user = $user->getUsername();
		if (isset($this->accounts[$user])) {
			unset($this->accounts[$user]);
		}	
	}
	
	public function getBuilder() : IAccountBuilder {
		return $this->accountBuilder;
	}
}
