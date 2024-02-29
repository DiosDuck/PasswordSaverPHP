<?php

namespace Repository;

use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;
use Exception\Account\NotFoundException;
use Exception\Account\AlreadyExistsException;
use Repository\IRepository\IAccountRepository;

class AccountRepository implements IAccountRepository {
	protected array $accounts;
	
	public function __construct() {
		$this->accounts = [];
	}
	
	public function add(IAccount $account) : IAccount {
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
	
	public function delete(IUser $user, string $domain) : IAccount {
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
	
	public function getAll(IUser $user) : array {
		$username = $user->getUsername();
		if (!isset($this->accounts[$username])) {
			return [];
		}
		return $this->accounts[$username];
	}
	
	public function getAllByDomain(IUser $user, string $domain) : array {
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
	
	public function update(IAccount $oldAccount, IAccount $newAccount) : IAccount {
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
	
	public function get(IUser $user, string $domain) : IAccount {
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
	
	public function deleteAll(IUser $user) : void {
		$user = $user->getUsername();
		if (isset($this->accounts[$user])) {
			unset($this->accounts[$user]);
		}	
	}
}
