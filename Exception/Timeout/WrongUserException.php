<?php

namespace Exception\Timeout;

use Exception\Timeout\TimeoutException;

class WrongUserException extends TimeoutException {
	public function __toString() : string {
		return "TimeoutException: wrong user class was used (who tf wrote this code?)";
	}
}