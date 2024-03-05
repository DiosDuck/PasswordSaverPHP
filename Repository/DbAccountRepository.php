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
use Mapper\DB\IMapper\IAccountDBMapper;

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
					$this->accountMapper->getInsertQuery()
				);
				
				$statement->execute(
					$this->accountMapper->getInsertParameters($account)
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
				$this->accountMapper->getDeleteQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getDeleteParameters($user, $domain)
			);
			
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
				$this->accountMapper->getDeleteByUserQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getDeleteByUserParameters($user)
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
	
	public function getAll(IUser $user) : array {
		try {
			$statement = $this->pdo->prepare(
				$this->accountMapper->getSelectQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getSelectParameters($user)
			);
			
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
				$this->accountMapper->getSelectMultipleDomainQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getSelectMultipleDomainParameters(
					$user, $domain
				)
			);
			
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
	
	public function update(IAccount $oldAccount, IAccount $newAccount) : IAccount {
		try {
			$this->get($oldAccount->getUser(), $oldAccount->getDomain());
			
			$this->pdo->beginTransaction();
			
			$statement = $this->pdo->prepare(
				$this->accountMapper->getUpdateQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getUpdateParameters($newAccount)
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
				$this->accountMapper->getOneSelectQuery()
			);
			
			$statement->execute(
				$this->accountMapper->getOneSelectParameters($user,$domain)
			);
			
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
				$this->accountMapper->getCreateTableQuery()
			);
			
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw new DBException($e);
		}
	}
}