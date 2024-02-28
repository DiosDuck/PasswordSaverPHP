<?php

namespace Exception\Authentification;

use Exception\Authentification\AuthentificationException;

class WrongPasswordException extends AuthentificationException {
	public function __toString() {
		return parent::__toString() . "Wrong password for the current user!";
	}
}