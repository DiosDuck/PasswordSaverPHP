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
				$this->userMapper->getSelectQuery()
			);
			
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
				$this->userMapper->getOneSelectQuery()
			);
			
			$statement->execute(
				$this->userMapper->getOneSelectParameters($username)
			);
			
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
					$this->userMapper->getInsertQuery()
				);
				
				$statement->execute(
					$this->userMapper->getInsertParameters($user)
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
				$this->userMapper->getDeleteQuery()
			);
			
			$statement->execute(
				$this->userMapper->getDeleteParameters($username)
			);
			
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
				$this->userMapper->getUpdateQuery()
			);
			$statement->execute(
				$this->userMapper->getUpdateParameters($newUser)
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
				$this->userMapper->getCreateTableQuery()
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
}