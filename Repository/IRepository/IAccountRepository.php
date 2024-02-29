<?php

namespace Repository\IRepository;

use Entity\IEntity\IUser;
use Entity\IEntity\IAccount;

interface IAccountRepository {
	public function add(IAccount $account) : IAccount;
	public function delete(IUser $user, string $domain) : IAccount;
	public function getAll(IUser $user) : array;
	public function getAllByDomain(IUser $user, string $domain) : array;
	public function update(IAccount $oldAccount, IAccount $newAccount) : IAccount;
	public function get(IUser $user, string $domain) : IAccount;
	public function deleteAll(IUser $user) : void;
}