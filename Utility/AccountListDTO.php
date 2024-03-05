<?php

namespace Utility;
use Entity\VisibilityAccount;

class AccountListDTO {
	private array $accounts;
	
	public function __construct(array $accounts = []) {
		$this->accounts = array_map(function($account) {return new VisibilityAccount($account);}, $accounts);
	}
	
	public function getAllAccounts() : array {
		$acc = [];
		foreach ($this->accounts as $account) {
			$acc[] = [
				$account->getDomain(), 
				$account->getUsername(), 
				$account->getPassword()
			];
		}
		return $acc;
	}
	
	public function getAccountDomainById(int $id) : ?string {
		if (isset($this->accounts[$id])) {
			return $this->accounts[$id]->getDomain();
		}
		return null;
	}

	public function showPassword(int $id) : void {
		$this->changeVisibility($id, true);
	}

	public function hidePassword(int $id) : void {
		$this->changeVisibility($id, false);
	}

	private function changeVisibility(int $id, bool $visibility) : void {
		if (isset($this->accounts[$id])) {
			$this->accounts[$id]->setVisibility($visibility);
		}
	}
}