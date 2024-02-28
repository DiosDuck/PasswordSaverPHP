<?php

namespace Repository;

use Builder\AccountBuilder;
use Builder\IBuilder\IAccountBuilder;
use Entity\Account;
use Entity\User;
use Repository\AccountRepository;

class FileAccountRepository extends AccountRepository {
	private string $dirPath;
	private AccountBuilder $accountBuilder = new AccountBuilder();
	
	public function __construct(string $dirPath) {
		parent::__construct();
		$this->dirPath = $dirPath;
		$this->readFromFile();
	}
	
	
	public function add(Account $account) : Account {
		$account = parent::add($account);
		$this->writeToFile($account->getUser()->getUsername());
		return $account;
	}
	public function delete(User $user, string $domain) : Account {
		$account = parent::delete($user, $domain);
		$this->writeToFile($user->getUsername());
		return $account;
	}
	public function update(Account $oldAccount, Account $newAccount) : Account {
		$account = parent::update($oldAccount, $newAccount);
		$this->writeToFile($oldAccount->getUser()->getUsername());
		return $account;
	}
	
	public function deleteAll(User $user) : void {
		parent::deleteAll($user);
		$username = $user->getUsername();
		$file = $this->dirPath . $username . '.txt';
		if (file_exists($file)) {
			unlink($file);
		}
	}

	public function getBuilder() : IAccountBuilder {
		return $this->accountBuilder;
	}
	
	private function readFromFile() : void {
		$files = array_diff(scandir($this->dirPath), array('.', '..'));
		foreach ($files as $file) {
			$user = str_replace(".txt", "", $file);
			$this->accounts[$user] = [];
			if (filesize($this->dirPath . $file)) {
				$streamFile = fopen($this->dirPath . $file, 'r');
				while(!feof($streamFile)) {
					$line = explode(';', str_replace("\n", "", fgets($streamFile)));
					
					$this->accountBuilder->createAccount();
					$this->accountBuilder->setDomain($line[0]);
					$this->accountBuilder->setUsername($line[1]);
					$this->accountBuilder->setPassword($line[2]);
					$account = $this->accountBuilder->getAccount();
					
					$this->accounts[$user][] = $account;
				}
				fclose($streamFile);
			}
		}
	}
	
	private function writeToFile(string $user) : void {
		$lines = [];
		foreach($this->accounts[$user] as $account) {
			$lines[] = $account->getDomain() . ';' . $account->getUsername() . ';' . $account->getPassword();
		}
		$output = implode("\n", $lines);
		$streamFile = fopen($this->dirPath . $user . '.txt', 'w');
		fputs($streamFile, $output);
		fclose($streamFile);
	}
}