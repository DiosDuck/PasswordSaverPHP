<?php

namespace Utility;

trait EncryptTrait {
	private string $key;
	private string $method = 'aes-256-cbc';
	
	public function getKey() : string {
		return $this->key;
	}
	
	public function setKey(string $key) : void {
		$this->key  = $key;
	}
	
	protected function generateKey() : string {
		$keyLen = openssl_cipher_iv_length($this->method);
		return openssl_random_pseudo_bytes($keyLen);
	}
	
	protected function encrypt(string $password) : string {
		return openssl_encrypt(
			$password, 
			$this->method, 
			$this->key, 
			false, 
			$this->key
		);
	}
	
	protected function decrypt(string $encrypted) : string {
		return openssl_decrypt(
			$encrypted, 
			$this->method, 
			$this->key, 
			false, 
			$this->key
		);
	}
}