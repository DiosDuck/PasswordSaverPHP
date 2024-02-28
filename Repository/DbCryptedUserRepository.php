<?php

namespace Repository;

use Exception\Type\UserTypeException;
use PDO;
use PDOException;
use Entity\User;
use Entity\CryptedUser;
use Repository\IRepository\IUserRepository;
use Exception\Authentification\AuthentificationException;
use Exception\Authentification\NotFoundException;
use Exception\Authentification\WrongPasswordException;
use Exception\Authentification\AlreadyExistsException;
use Exception\DB\DBException;
use Builder\IBuilder\IUserBuilder;
use Builder\CryptedUserBuilder;

class DbCryptedUserRepository implements IUserRepository {
	private PDO $pdo;
	private CryptedUserBuilder $userBuilder;
	
	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
		$this->userBuilder = new CryptedUserBuilder();
		$this->createTableIfNotExist();
	}
	
	public function getAll() : array {
		try {
			$statement = $this->pdo->prepare(
				'Select username, name, password, key from users');
			
			$statement->execute([]);
			
			$data = $statement->fetchAll();
			$users = [];
			
			foreach ($data as $row) {
				$this->userBuilder->createUser();
				$this->userBuilder->setUsername($row['username']);
				$this->userBuilder->setName($row['name']);
				$this->userBuilder->setRawPassword($row['password']);
				$this->userBuilder->setKey($row['key']);
				
				$users[] = $this->userBuilder->getUser();
			}
			
			return $users;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function get(string $username, string $password) : User {
		try {
			$statement = $this->pdo->prepare(
				'Select username, name, password, key from users
				where username = :username');
			
			$statement->execute([
				'username' => $username
			]);
			
			$data = $statement->fetchAll();
			if (count($data) != 1) {
				throw new NotFoundException();
			}				
			
			$data = $data[0];
			
			$this->userBuilder->createUser();
			$this->userBuilder->setUsername($data['username']);
			$this->userBuilder->setName($data['name']);
			$this->userBuilder->setRawPassword($data['password']);
			$this->userBuilder->setKey($data['key']);
			$user = $this->userBuilder->getUser();
			
			if ($user->getPassword() != $password) {
				throw new WrongPasswordException();
			}
			
			return $user;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function add(User $user) : User {
		if (!$user instanceof CryptedUser) {
			throw new UserTypeException();
		}

		try {
			try {
				$this->get($user->getUsername(), $user->getPassword());
				throw new AlreadyExistsException();
			} catch (NotFoundException $e) {
				$this->pdo->beginTransaction();
				
				$statement = $this->pdo->prepare(
					'Insert into users(username, name, password, key)
					values (:username, :name, :password, :key)');
				
				$statement->execute([
					'username' => $user->getUsername(),
					'name' => $user->getName(),
					'password' => $user->getRawPassword(),
					'key' => $user->getKey()
				]);
				
				$this->pdo->commit();
				return $user;
			} catch (AuthentificationException $ae) {
				throw new AlreadyExistsException();
			}
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function delete(string $username, string $password) : User {
		try {
			$user = $this->get($username, $password);
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'DELETE FROM users
				WHERE username = :username');
			
			$statement->execute([
				'username' => $username
			]);
			
			$this->pdo->commit();
			return $user;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function update(User $oldUser, User $newUser) : User {
		if (!$newUser instanceof CryptedUser) {
			throw new UserTypeException();
		}
		
		try {
			$this->get($oldUser->getUsername(), $oldUser->getPassword());
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'UPDATE users
				SET name = :name, password = :password, key = :key
				WHERE username = :username
				');
			
			$statement->execute([
				'username' => $newUser->getUsername(),
				'name' => $newUser->getName(),
				'password' => $newUser->getRawPassword(),
				'key' => $newUser->getKey()
			]);
			
			$this->pdo->commit();
			return $newUser;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function getBuilder() : IUserBuilder {
		return $this->userBuilder;
	}
	
	private function createTableIfNotExist() {
		try {
			$this->pdo->beginTransaction();
			
			$this->pdo->exec(
				"CREATE TABLE IF NOT EXISTS users 
				(
					username varchar(255) PRIMARY KEY,
					name varchar(255),
					password varchar(255),
					key varchar(255)
				)"
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
}