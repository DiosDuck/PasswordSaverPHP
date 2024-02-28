<?php

namespace Utility;

use Entity\User;
use Entity\TimeoutUser;
use Exception\Timeout\TimeoutException;
use Exception\Timeout\WrongUserException;

trait TimeoutCheckTrait {
	public function checkUserValid(User $user) : TimeoutUser {
		if (!$user  instanceof TimeoutUser) {
			throw new WrongUserException();
		}
		if (time() - $user->getTime() > 60) {
			throw new TimeoutException();
		}

		return $user;
	}
	
	public function updateUser(TimeoutUser $user) : void {
		$user->updateTime();
	}
}