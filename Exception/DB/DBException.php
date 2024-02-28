<?php

namespace Exception\DB;

class DBException extends \Exception {
	private \PDOException $ex;
	
	public function __construct(\PDOException $ex) {
		$this->ex = $ex;
	}
	
	public function getException() : \PDOException {
		return $this->ex;
	}
	
	public function __toString() :string {
		return "DBException: please contact someone to administrate the error";
	}
}