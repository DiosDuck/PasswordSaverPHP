<?php

namespace Repository\IRepository;

use Entity\User;
use Entity\Account;
use Builder\IBuilder\IAccountBuilder;

interface IAccountRepository {
	public function add(Account $account) : Account;
	public function delete(User $user, string $domain) : Account;
	public function getAll(User $user) : array;
	public function getAllByDomain(User $user, string $domain) : array;
	public function update(Account $oldAccount, Account $newAccount) : Account;
	public function get(User $user, string $domain) : Account;
	public function deleteAll(User $user) : void;
	public function getBuilder() : IAccountBuilder;
}