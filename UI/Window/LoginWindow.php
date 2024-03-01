<?php

namespace UI\Window;

use UI\Window\Window;
use Service\IService\IUserService;
use Exception\Authentification\AuthentificationException;

class LoginWindow extends Window {
    private IUserService $userService;
    
    public function setUserService(IUserService $userService) : void {
        $this->userService = $userService;
    }

    public function setAccountService(\Service\IService\IAccountService $accountService) : void {
        //do nothing
    }

    public function run() : ?Window {
		$this->clearConsole();
		$output = $this->callMethod(['welcomePage']);
		while (true) {
			try {
				$this->clearConsole();
				switch ($output[0]) {
                    case Window::WINDOW_EXIT:
                        return null;
                    case Window::WINDOW_MOVE:
                        return $output[1];
                    default:
                        $output = $this->callMethod($output);
                }
            }
			catch (AuthentificationException $e) {
				$this->clearConsole();
				$this->printErrorMessage($e);
				$output = $this->callMethod(['welcomePage']);
			}
		}
    }

    public function welcomePage() : array {
		$this->printDisplayText(
			"Welcome!",
			"Use the numbers to select your next command",
			["Login", "Create new user"]
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return [Window::WINDOW_EXIT];
			case '1':
				return ['login'];
			case '2':
				return ['register'];
			default:
				return ['welcomePage'];
		}
	}

    public function login() : array {	
		$this->printDisplayText(
			"Login",
			"Please enter the details down bellow:"
		);
		
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		$user = $this->userService->logIn($username, $password);
		
		return [Window::WINDOW_MOVE, new ManageUserWindow($user)];
	}
    public function register() : array {	
		$this->printDisplayText(
			"Register",
			"Please enter the details down bellow:"
		);
		
		$name = $this->whileNotEmptyInput("Name: ");
		$username = $this->whileNotEmptyInput("Username (must be unique): ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		return ['confirmCreateUser', $name, $username, $password];
	}
	
	public function confirmCreateUser(string $name, string $username, string $password) : array {
		$this->printConfirmation(
			"Are you sure you want to create this user?\n\nName: $name\nUsername: $username\nPassword: $password",
			['Cancel', 'Yes', 'No']
		);
		
        $input = readline("Select: ");
		switch($input){
			case '0':
				return ['welcomePage'];
			case '1':
				$this->userService->createNewUser($name, $username, $password);
				return ['welcomePage'];
			case '2':
				return ['register'];
			default:
				return ['confirmCreateUser', $name, $username, $password];
		}
	}
}