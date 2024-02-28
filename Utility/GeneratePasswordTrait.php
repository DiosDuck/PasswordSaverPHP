<?php

namespace Utility;

trait GeneratePasswordTrait {
	private string $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	private string $specialCharacters = '!@#$%^&*';
	
	public function generatePassword(int $size = 16, bool $allowSpecialCharacters = true) : string {
		$string = $this->characters;
		if ($allowSpecialCharacters) {
			$string .= $this->specialCharacters;
		}
		
		$max = strlen($string) - 1;
		$pass = '';
		for ($i = 0; $i < $size; $i++) {
			$pass .= $string[random_int(0, $max)];
		}
		
		return $pass;
	}
}