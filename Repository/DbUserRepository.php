<?php

namespace Repository;

use PDO;
use PDOException;
use Entity\IEntity\IUser;
use Repository\IRepository\IUserRepository;
use Exception\Authentification\AuthentificationException;
use Exception\Authentification\NotFoundException;
use Exception\Authentification\WrongPasswordException;
use Exception\Authentification\AlreadyExistsException;
use Exception\DB\DBException;
use Mapper\IMapper\IUserDBMapper;

class DbUserRepository implements IUserRepository {
	private PDO $pdo;
	private IUserDBMapper $userMapper;
	
	public function __construct(PDO $pdo, IUserDBMapper $userMapper) {
		$this->pdo = $pdo;
		$this->userMapper = $userMapper;
		$this->createTableIfNotExist();
	}
	
	public function getAll() : array {
		try {
			$statement = $this->pdo->prepare(
				'Select ' . $this->userMapper->getDbParameters() . ' from users');
			
			$statement->execute([]);
			
			$data = $statement->fetchAll();
			$users = [];
			
			foreach ($data as $row) {
				$users[] = $this->userMapper->getUser($row);
			}
			
			return $users;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function get(string $username, string $password) : IUser {
		try {
			$statement = $this->pdo->prepare(
				'Select ' . $this->userMapper->getDbParameters() . ' from users
				where username = :username');
			
			$statement->execute([
				'username' => $username
			]);
			
			$data = $statement->fetchAll();
			if (count($data) != 1) {
				throw new NotFoundException();
			}				
			
			$user = $this->userMapper->getUser($data[0]);
			
			if ($user->getPassword() != $password) {
				throw new WrongPasswordException();
			}
			
			return $user;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function add(IUser $user) : IUser {
		try {
			try {
				$this->get($user->getUsername(), $user->getPassword());
				throw new AlreadyExistsException();
			} catch (NotFoundException $e) {
				$this->pdo->beginTransaction();
				
				$statement = $this->pdo->prepare(
					'Insert into users('. $this->userMapper->getDbParameters() . ')
					values (' . $this->userMapper->getDbInsertParameters() . ')');
				
				$statement->execute(
					$this->userMapper->getExecutableParameters($user)
				);
				
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
	
	public function delete(string $username, string $password) : IUser {
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
	
	public function update(IUser $oldUser, IUser $newUser) : IUser {
		try {
			$this->get($oldUser->getUsername(), $oldUser->getPassword());
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'UPDATE users
				SET ' . $this->userMapper->getDbUpdateParameters() . '
				WHERE username = :username'
			);
			$statement->execute(
				$this->userMapper->getExecutableParameters($newUser)
			);
			
			$this->pdo->commit();
			return $newUser;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	private function createTableIfNotExist() {
		try {
			$this->pdo->beginTransaction();
			
			$this->pdo->exec(
				'CREATE TABLE IF NOT EXISTS users 
				( username varchar(255) PRIMARY KEY' . $this->userMapper->getCreateTableNonKeyParameters() . ')'
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
}