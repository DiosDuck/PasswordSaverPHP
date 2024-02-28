<?php

namespace Exception\Account;

use Exception\Account\AccountException;

class AlreadyExistsException extends AccountException {
	public function __toString() {
		return parent::__toString() . "The domain already exist!";
	}
}