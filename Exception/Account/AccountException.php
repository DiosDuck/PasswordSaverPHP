<?php

namespace Exception\Account;

class AccountException extends \Exception {
	public function __toString()
	{
		return "Account Exception: ";
	}
}