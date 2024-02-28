<?php

namespace Exception\Authentification;

use Exception\Authentification\AuthentificationException;

class NotFoundException extends AuthentificationException {
	public function __toString() {
		return parent::__toString() . "The user does not exist!";
	}
}