<?php

namespace Repository;

use Entity\IEntity\IUser;
use Mapper\IMapper\IUserFileMapper;
use Repository\UserRepository;

class FileUserRepository extends UserRepository {
	private string $file;
	private IUserFileMapper $UserFileMapper;
	
	public function __construct(string $file, IUserFileMapper $UserFileMapper) {
		parent::__construct();
		$this->file = $file;
		$this->UserFileMapper = $UserFileMapper;
		$this->readFromFile();
	}
	
	public function add(IUser $newUser) : IUser {
		$user = parent::add($newUser);
		$this->writeToFile();
		return $user;
	}
	
	public function update(IUser $oldUser, IUser $newUser) : IUser {
		$user = parent::update($oldUser, $newUser);
		$this->writeToFile();
		return $user;
	}
	
	public function delete(string $username, string $password) : IUser {
		$user = parent::delete($username, $password);
		$this->writeToFile();
		return $user;
	}
	
	private function readFromFile() {
		if (file_exists($this->file)) {
			$streamFile = fopen($this->file, 'r');
			while(!feof($streamFile)) {
				$line = str_replace("\n", "", fgets($streamFile));

				$user = $this->UserFileMapper->getUser($line);
				
				$this->users[] = $user;
			}
			fclose($streamFile);				
		}
	}
	
	private function writeToFile() {
		$lines = [];
		foreach ($this->users as $user) {
			$lines[] = $this->UserFileMapper->getLine($user);
		}
		$output = implode("\n", $lines);
		$streamFile = fopen($this->file, 'w');
		fputs($streamFile, $output);
		fclose($streamFile);
	}
}