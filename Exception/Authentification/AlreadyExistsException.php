<?php

namespace Exception\Authentification;

use Exception\Authentification\AuthentificationException;

class AlreadyExistsException extends AuthentificationException {
	public function __toString() {
		return parent::__toString() . "The user already exist!";
	}
}