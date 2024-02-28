<?php

namespace Exception\Timeout;

class TimeoutException extends \Exception {
	public function __toString() : string 
	{
		return "TimeoutException: The user got timeout, please log int";
	}
}