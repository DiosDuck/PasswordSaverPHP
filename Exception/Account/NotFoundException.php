<?php

namespace Exception\Account;

use Exception\Account\AccountException;

class NotFoundException extends AccountException {
	public function __toString() {
		return parent::__toString() . "The account does not exist!";
	}
}