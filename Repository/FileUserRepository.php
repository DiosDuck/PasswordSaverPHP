<?php

namespace Repository;

use Builder\IBuilder\IUserBuilder;
use Builder\UserBuilder;
use Entity\User;
use Repository\UserRepository;

class FileUserRepository extends UserRepository {
	private string $file;
	private UserBuilder $userBuilder = new UserBuilder();
	
	public function __construct(string $file) {
		parent::__construct();
		$this->file = $file;
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
		if (file_exists($this->file)) {
			$streamFile = fopen($this->file, 'r');
			while(!feof($streamFile)) {
				$line = explode(';', str_replace("\n", "", fgets($streamFile)));
				
				$this->userBuilder->createUser();
				$this->userBuilder->setName($line[0]);
				$this->userBuilder->setUsername($line[1]);
				$this->userBuilder->setPassword($line[2]);
				$user = $this->userBuilder->getUser();
				
				$this->users[] = $user;
			}
			fclose($streamFile);				
		}
	}
	
	private function writeToFile() {
		$lines = [];
		foreach ($this->users as $user) {
			$lines[] = $user->getName() . ';' . $user->getUsername() . ';' .$user->getPassword();
		}
		$output = implode("\n", $lines);
		$streamFile = fopen($this->file, 'w');
		fputs($streamFile, $output);
		fclose($streamFile);
	}
}