<?php

namespace Repository;

use Entity\IEntity\IAccount;
use Entity\IEntity\IUser;
use Mapper\File\IMapper\IAccountFileMapper;
use Repository\AccountRepository;

class FileAccountRepository extends AccountRepository {
	private string $dirPath;
	private IAccountFileMapper $AccountFileMapper;
	
	public function __construct(string $dirPath, IAccountFileMapper $AccountFileMapper) {
		parent::__construct();
		$this->dirPath = $dirPath;
		$this->AccountFileMapper = $AccountFileMapper;
		$this->readFromFile();
	}
	
	
	public function add(IAccount $account) : IAccount {
		$account = parent::add($account);
		$this->writeToFile($account->getUser()->getUsername());
		return $account;
	}
	public function delete(IUser $user, string $domain) : IAccount {
		$account = parent::delete($user, $domain);
		$this->writeToFile($user->getUsername());
		return $account;
	}
	public function update(IAccount $oldAccount, IAccount $newAccount) : IAccount {
		$account = parent::update($oldAccount, $newAccount);
		$this->writeToFile($oldAccount->getUser()->getUsername());
		return $account;
	}
	
	public function deleteAll(IUser $user) : void {
		parent::deleteAll($user);
		$username = $user->getUsername();
		$file = $this->dirPath . $username . '.txt';
		if (file_exists($file)) {
			unlink($file);
		}
	}
	
	private function readFromFile() : void {
		$files = array_diff(scandir($this->dirPath), array('.', '..'));
		foreach ($files as $file) {
			$user = str_replace(".txt", "", $file);
			$this->accounts[$user] = [];
			if (filesize($this->dirPath . $file)) {
				$streamFile = fopen($this->dirPath . $file, 'r');
				while(!feof($streamFile)) {
					$line = str_replace("\n", "", fgets($streamFile));

					$account = $this->AccountFileMapper->getAccount($line);
					
					$this->accounts[$user][] = $account;
				}
				fclose($streamFile);
			}
		}
	}
	
	private function writeToFile(string $user) : void {
		$lines = [];
		foreach($this->accounts[$user] as $account) {
			$lines[] = $this->AccountFileMapper->getLine($account);
		}
		$output = implode("\n", $lines);
		$streamFile = fopen($this->dirPath . $user . '.txt', 'w');
		fputs($streamFile, $output);
		fclose($streamFile);
	}
}