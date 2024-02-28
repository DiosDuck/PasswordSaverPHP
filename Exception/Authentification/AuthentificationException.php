<?php

namespace Exception\Authentification;

class AuthentificationException extends \Exception {
	public function __toString()
	{
		return "Authentification Exception: ";
	}
}