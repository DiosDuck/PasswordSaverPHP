<?php

namespace Repository;

use PDO;
use PDOException;
use Entity\IEntity\IAccount;
use Entity\IEntity\IUser;
use Exception\Account\AccountException;
use Exception\Account\NotFoundException;
use Exception\Account\AlreadyExistsException;
use Repository\IRepository\IAccountRepository;
use Exception\DB\DBException;
use Mapper\IMapper\IAccountDBMapper;

class DbAccountRepository implements IAccountRepository {
	private PDO $pdo;
	private IAccountDBMapper $accountMapper;

	public function __construct(PDO $pdo, IAccountDBMapper $accountMapper) {
		$this->pdo = $pdo;
		$this->accountMapper = $accountMapper;
		$this->createTableIfNotExist();
	}
	
	public function add(IAccount $account) : IAccount {
		try {
			try {
				$this->get($account->getUser(), $account->getDomain());
				throw new AlreadyExistsException();
			} catch (NotFoundException $e) {
				$this->pdo->beginTransaction();
				
				$statement = $this->pdo->prepare(
					'Insert into accounts(' . $this->accountMapper->getDbParameters() . ')
					values ( '. $this->accountMapper->getDbInsertParameters() . ' )');
				
				$statement->execute(
					$this->accountMapper->getExecutableParameters($account)
				);
				
				$this->pdo->commit();
				return $account;			
			} catch (AccountException $ae) {
				throw new AlreadyExistsException();
			}
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function delete(IUser $user, string $domain) : IAccount {
		try {
			$acc = $this->get($user, $domain);
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'DELETE from accounts
				where user = :user and domain = :domain');
			
			$statement->execute([
				'user' => $user->getUsername(),
				'domain' => $domain
			]);
			
			$this->pdo->commit();
			
			return $acc;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	
	public function deleteAll(IUser $user) : void {
		try {
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'DELETE from accounts
				where user = :user');
			
			$statement->execute([
				'user' => $user->getUsername()
			]);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function getAll(IUser $user) : array {
		try {
			$statement = $this->pdo->prepare(
				'Select ' . $this->accountMapper->getDbParameters() . ' 
				from accounts
				where user = :user');
			
			$statement->execute([
				'user' => $user->getUsername()
			]);
			
			$data = $statement->fetchAll();
			$accounts = [];
			
			foreach ($data as $row) {
				$accounts[] = $this->accountMapper->getAccount($row, $user);
			}
			
			return $accounts;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function getAllByDomain(IUser $user, string $domain) : array {
		if ($domain == '') {
			return $this->getAll($user);
		}
		try {
			$statement = $this->pdo->prepare(
				'Select ' . $this->accountMapper->getDbParameters() . ' 
				from accounts
				where user = :user and domain like :domain');
			
			$statement->execute([
				'user' => $user->getUsername(),
				'domain' => '%' . $domain . '%'
			]);
			
			$data = $statement->fetchAll();
			$accounts = [];
			
			foreach ($data as $row) {
				$accounts[] = $this->accountMapper->getAccount($row, $user);
			}
			
			return $accounts;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function update(IAccount $oldAccount, IAccount $newAccount) : IAccount {
		try {
			$this->get($oldAccount->getUser(), $oldAccount->getDomain());
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'UPDATE accounts
				SET '. $this->accountMapper->getDbUpdateParameters() . '
				WHERE domain = :domain and user = :user');
			
			$statement->execute(
				$this->accountMapper->getExecutableParameters($newAccount)
			);
			
			$this->pdo->commit();
			return $newAccount;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function get(IUser $user, string $domain) : IAccount {
		try {
			$statement = $this->pdo->prepare(
				'Select ' . $this->accountMapper->getDbParameters() . '
				from accounts
				where user = :user and domain = :domain');
			
			$statement->execute([
				'user' => $user->getUsername(),
				'domain' => $domain
			]);
			
			$data = $statement->fetchAll();
			if (count ($data) != 1) {
				throw new NotFoundException();
			}
			
			$account = $this->accountMapper->getAccount($data[0], $user);
			
			return $account;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	private function createTableIfNotExist() {
		try {
			$this->pdo->beginTransaction();
			
			$this->pdo->exec(
				'CREATE TABLE IF NOT EXISTS accounts 
				(
					domain varchar(255) PRIMARY KEY' . $this->accountMapper->getCreateTableNonKeyParameters() . ' 
					,user varchar(255), FOREIGN KEY (user) REFERENCES users(username)
				)'
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
}