<?php

namespace UI\Window;

use Entity\IEntity\IUser;
use Exception\Timeout\TimeoutException;
use Exception\Authentification\AuthentificationException;
use Service\IService\IUserService;
use Service\IService\IAccountService;

class ManageUserWindow extends Window {
    private IUser $user;
    private IUserService $userService;
    private IAccountService $accountService;

    public function __construct(IUser $user) {
        $this->user = $user;
    }

    public function setUserService(IUserService $userService) : void {
        $this->userService = $userService;
    }

    public function setAccountService(IAccountService $accountService) : void {
        $this->accountService = $accountService;
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
            catch (AuthentificationException|TimeoutException $e) {
				$output = [Window::WINDOW_MOVE, new LoginWindow((string) $e)];
            }
		}
    }
    
    public function welcomePage() : array {
		$this->printDisplayText(
			"Welcome," . $this->user->getName() . "!",
			"How can I help you?",
			["Manage accounts", "Change password", "Change name", "Delete the user", "Logout"]
		);
		
        $input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['exit'];
			case '1':
				return ['manageAccounts'];
			case '2':
				return ['changePassword'];
			case '3':
				return ['changeName'];
			case '4':
				return ['deleteUser'];
			case '5':
				return ['logout'];
			default:
				return ['welcomePage'];
		}	
    }

	public function manageAccounts() : array {
		return [Window::WINDOW_MOVE, new ManageAccountWindow($this->user)];
	}
	
	public function changePassword() : array {
		$this->printDisplayText(
			"Change " . $this->user->getName() .  "'s password",
			"Please enter the details down bellow:"
		);
		
		$oldPassword = $this->whileNotEmptyInput("Old Password: ");
		$newPassword = $this->whileNotEmptyInput("New Password: ");
		
		return ['confirmChangePassword', $oldPassword, $newPassword];
	}
	
	public function confirmChangePassword(string $oldPassword, string $newPassword) : array {
		$this->printConfirmation(
			"Are you sure you want to change the password to '$newPassword'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case 0:
				return ['welcomePage'];
			case 1:
				$this->user = $this->userService->changePassword(
						$this->user, 
						$oldPassword, 
						$newPassword
					);
				return ['welcomePage'];
			case 2:
				return ['changePassword'];
			default:
				return ['confirmChangePassword', $oldPassword, $newPassword];
		}
	}
	
	public function deleteUser() : array {
		$this->printDisplayText(
			"Delete " . $this->user->getName(),
			"Please enter the password down bellow:"
		);
		
		$password = $this->whileNotEmptyInput("Password :");
		return ['confirmDeleteUser', $password];
	}
	
	public function confirmDeleteUser(string $password) : array {
		$this->printConfirmation(
			"Are you sure you want to delete the user? All the data cannot be recovered",
			['No', 'Yes']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case 0:
				return ['welcomePage'];
			case 1:
				$this->accountService->deleteUser(
					$this->user
				);
				$this->userService->deleteUser(
					$this->user, 
					$password
				);
				return [Window::WINDOW_MOVE, new LoginWindow()];
			default:
				return ['confirmDeleteUser', $password];
		}
	}
	
	public function changeName() : array {
		$this->printDisplayText(
			"Change " . $this->user->getName() .  "'s name",
			"Please enter the details down bellow:"
		);
		
		$password = $this->whileNotEmptyInput("Password: ");
		$newName = $this->whileNotEmptyInput("New name: ");
		
		return ['confirmChangeName', $password, $newName];
	}
	
	public function confirmChangeName(string $password, string $newName) : array {
		$this->printConfirmation(
			"Are you sure you want to changt the name to '$newName'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case 0:
				return ['welcomePage'];
			case 1:
				$this->user = $this->userService->changeName(
						$this->user, 
						$password, 
						$newName
					);
				return ['welcomePage'];
			case 2:
				return ['changeName'];
			default:
				return ['confirmChangeName', $password, $newName];
		}
	}
	
	public function login() : array {	
		$this->printDisplayText(
			"Login",
			"Please enter the details down bellow:"
		);
		
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		$this->user = $this->userService->logIn($username, $password);
		
		return ['welcomePage'];
	}
	
	public function logout() : array {
		return [Window::WINDOW_MOVE, new LoginWindow()];
	}
}