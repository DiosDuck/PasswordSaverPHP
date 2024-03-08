<?php

namespace Utility;

use Entity\IEntity\IUser;
use Entity\TimeoutUser;
use Exception\Timeout\TimeoutException;
use Exception\Type\UserTypeException;

trait TimeoutCheckTrait {
	public function checkUserValid(IUser $user) : TimeoutUser {
		if (!$user  instanceof TimeoutUser) {
			throw new UserTypeException();
		}
		if (time() - $user->getTime() > 60 * 5) {
			throw new TimeoutException();
		}

		return $user;
	}
	
	public function updateUser(TimeoutUser $user) : void {
		$user->updateTime();
	}
}