<?php

namespace Utility;

class AccountListDTO {
	private array $accounts;
	
	public function __construct(array $accounts = []) {
		$this->accounts = $accounts;
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
}