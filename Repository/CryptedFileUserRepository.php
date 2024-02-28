<?php

namespace Repository;

use Entity\User;
use Entity\CryptedUser;
use Repository\UserRepository;
use Builder\CryptedUserBuilder;
use Builder\IBuilder\IUserBuilder;
use Builder\UserBuilder;

class CryptedFileUserRepository extends UserRepository {
	private string $file;
	private CryptedUserBuilder $userBuilder = new CryptedUserBuilder();
	
	public function __construct(string $file) {
		parent::__construct();
		$this->file = $file;
		$this->userBuilder = new CryptedUserBuilder();
		$this->readFromFile();
	}
	
	public function add(User $newUser) : User {
		$user = parent::add($newUser);
		$this->writeToFile();
		return $user;
	}
	
	public function update(User $oldUser, User $newUser) : User {
		$user = parent::update($oldUser, $newUser);
		$this->writeToFile();
		return $user;
	}
	
	public function delete(string $username, string $password) : User {
		$user = parent::delete($username, $password);
		$this->writeToFile();
		return $user;
	}

	public function getBuilder() : IUserBuilder {
		return $this->userBuilder;
	}
	
	private function readFromFile() {
		if (file_exists($this->file) && filesize($this->file)) {
			$streamFile = fopen($this->file, 'r');
			while(!feof($streamFile)) {
				$line = explode(';', str_replace("\n", "", fgets($streamFile)));
				
				$this->userBuilder->createUser();
				$this->userBuilder->setName($line[0]);
				$this->userBuilder->setUsername($line[1]);
				$this->userBuilder->setRawPassword($line[2]);
				$this->userBuilder->setKey(hex2bin($line[3]));
				$user = $this->userBuilder->getUser();
				
				$this->users[] = $user;
			}
			fclose($streamFile);				
		}
	}
	
	private function writeToFile() {
		$lines = [];
		foreach ($this->users as $user) {
			$lines[] = $user->getName() . ';' . $user->getUsername() . ';' .$user->getRawPassword() . ';' . bin2hex($user->getKey());
		}
		$output = implode("\n", $lines);
		$streamFile = fopen($this->file, 'w');
		fputs($streamFile, $output);
		fclose($streamFile);
	}
}