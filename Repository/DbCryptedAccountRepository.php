<?php

namespace Repository;

use Exception\Type\AccountTypeException;
use PDO;
use PDOException;
use Entity\User;
use Entity\Account;
use Entity\CryptedAccount;
use Exception\Account\AccountException;
use Exception\Account\NotFoundException;
use Exception\Account\AlreadyExistsException;
use Repository\IRepository\IAccountRepository;
use Exception\DB\DBException;
use Builder\IBuilder\IAccountBuilder;
use Builder\CryptedAccountBuilder;

class DbCryptedAccountRepository implements IAccountRepository {
	private PDO $pdo;
	private CryptedAccountBuilder $accountBuilder;
	
	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
		$this->accountBuilder = new CryptedAccountBuilder();
		$this->createTableIfNotExist();
	}
	
	public function add(Account $account) : Account {
		if (!$account instanceof CryptedAccount) {
			throw new AccountTypeException();
		}
		
		try {
			try {
				$this->get($account->getUser(), $account->getDomain());
				throw new AlreadyExistsException();
			} catch (NotFoundException $e) {
				$this->pdo->beginTransaction();
				
				$statement = $this->pdo->prepare(
					'Insert into accounts(domain, username, password, key, user)
					values (:domain, :username, :password, :key, :user)');
				
				$statement->execute([
					'username' => $account->getUsername(),
					'domain' => $account->getDomain(),
					'password' => $account->getRawPassword(),
					'key' => $account->getKey(),
					'user' => $account->getUser()->getUsername()
				]);
				
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
	
	public function delete(User $user, string $domain) : Account {
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
	
	
	public function deleteAll(User $user) : void {
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
	
	public function getAll(User $user) : array {
		try {
			$statement = $this->pdo->prepare(
				'Select domain, username, password, key 
				from accounts
				where user = :user');
			
			$statement->execute([
				'user' => $user->getUsername()
			]);
			
			$data = $statement->fetchAll();
			$accounts = [];
			
			foreach ($data as $row) {
				$this->accountBuilder->createAccount();
				$this->accountBuilder->setUsername($row['username']);
				$this->accountBuilder->setDomain($row['domain']);
				$this->accountBuilder->setRawPassword($row['password']);
				$this->accountBuilder->setKey($row['key']);
				$this->accountBuilder->setUser($user);
				$account = $this->accountBuilder->getAccount();
				
				$accounts[] = $account;
			}
			
			return $accounts;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function getAllByDomain(User $user, string $domain) : array {
		if ($domain == '') {
			return $this->getAll($user);
		}
		try {
			$statement = $this->pdo->prepare(
				'Select domain, username, password, key 
				from accounts
				where user = :user and domain like :domain');
			
			$statement->execute([
				'user' => $user->getUsername(),
				'domain' => '%' . $domain . '%'
			]);
			
			$data = $statement->fetchAll();
			$accounts = [];
			
			foreach ($data as $row) {
				$this->accountBuilder->createAccount();
				$this->accountBuilder->setUsername($row['username']);
				$this->accountBuilder->setDomain($row['domain']);
				$this->accountBuilder->setRawPassword($row['password']);
				$this->accountBuilder->setKey($row['key']);
				$this->accountBuilder->setUser($user);
				$account = $this->accountBuilder->getAccount();
				
				$accounts[] = $account;
			}
			
			return $accounts;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function update(Account $oldAccount, Account $newAccount) : Account {
		if (!$newAccount instanceof CryptedAccount) {
			throw new AccountTypeException();
		}

		try {
			$this->get($oldAccount->getUser(), $oldAccount->getDomain());
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				'UPDATE accounts
				SET password = :password, key = :key
				WHERE domain = :domain and user = :user');
			
			$statement->execute([
				'domain' => $newAccount->getDomain(),
				'password' => $newAccount->getRawPassword(),
				'key' => $newAccount->getKey(),
				'user' => $newAccount->getUser()->getUsername()
			]);
			
			$this->pdo->commit();
			return $newAccount;
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function get(User $user, string $domain) : Account {
		try {
			$statement = $this->pdo->prepare(
				'Select domain, username, password, key 
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
			
			$data = $data[0];
			
			$this->accountBuilder->createAccount();
			$this->accountBuilder->setUsername($data['username']);
			$this->accountBuilder->setDomain($data['domain']);
			$this->accountBuilder->setRawPassword($data['password']);
			$this->accountBuilder->setKey($data['key']);
			$this->accountBuilder->setUser($user);
			$account = $this->accountBuilder->getAccount();
			
			return $account;
		} catch (PDOException $e) {
			throw new DBException($e);
		}
	}
	
	public function getBuilder() : IAccountBuilder {
		return $this->accountBuilder;
	}
	
	private function createTableIfNotExist() {
		try {
			$this->pdo->beginTransaction();
			
			$this->pdo->exec(
				"CREATE TABLE IF NOT EXISTS accounts 
				(
					domain varchar(255) PRIMARY KEY,
					username varchar(255),
					password varchar(255),
					key varchar(255),
					user varchar(255),
					FOREIGN KEY (user) REFERENCES users(username)
				)"
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
}