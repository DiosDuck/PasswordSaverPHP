<?php

namespace UI\Window;

use Entity\IEntity\IUser;
use Exception\Account\AccountException;
use Service\IService\IAccountService;
use Service\IService\IUserService;
use Exception\Timeout\TimeoutException;
use Utility\GeneratePasswordTrait;

class ManageAccountWindow extends Window {
    private IUser $user;
    private IAccountService $accountService;
	
	use GeneratePasswordTrait;

    public function __construct(IUser $user) {
        $this->user = $user;
    }

    public function setUserService(IUserService $userService) : void {
        //do nothing
    }

    public function setAccountService(IAccountService $accountService) : void {
        $this->accountService = $accountService;
    }

    public function run() : ?Window {
        $this->clearConsole();
		$output = $this->callMethod(['manageAccounts']);
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
			catch (TimeoutException $e) {
				$output = [Window::WINDOW_MOVE, new LoginWindow((string) $e)];
			}
            catch (AccountException $e) {
				$this->clearConsole();
				$this->printErrorMessage($e);
				$output = $this->callMethod(['manageAccounts']);
            }
		}
    }

    public function manageAccounts() : array {
		$this->printDisplayText(
			"Manage accounts",
			"How can I help you?",
			[
				"Add new account", 
				"Add new account and generate password",
				"Get accounts by domain", 
				"Return to welcome page"
			]
		);
		
		$input = readline("Select: ");
		switch($input) {
			case '0':
				return [Window::WINDOW_EXIT];
			case '1':
				return ['addAccount'];
			case '2':
				return ['addAccountWithNewPassword'];
			case '3':
				return ['getAccountsByDomain'];
			case '4':
				return ['backToManageUser'];
			default:
				return ['manageAccounts'];
		}
	}
	
	public function addAccount() : array {
		$this->printDisplayText(
			"Add new account",
			"Please enter the details bellow"
		);
		
		$domain = $this->whileNotEmptyInput("Domain (make it unique): ");
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		return ['confirmAddAccount', $domain, $username, $password];
	}
	
	public function addAccountWithNewPassword() : array {
		$this->printDisplayText(
			"Add new account and generate a new password",
			"Please enter the details bellow"
		);
		
		$domain = $this->whileNotEmptyInput("Domain (make it unique): ");
		$username = $this->whileNotEmptyInput("Username: ");
		
		return ['addAccountGeneratedPassword', $domain, $username];
	}

    public function addAccountGeneratedPassword(string $domain, string $username) : array {
		$this->printConfirmation(
			'Do you want to customize the password generator? (the default lenght is 16 and incluse special characters)',
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['manageAccounts'];
			case '1':
				return ['setSizePasswordGenerator', $domain, $username];
			case '2':
				$password = $this->generatePassword();
				return ['confirmAddAccountWithNewPassword', $domain, $username, $password];
			default:
				return ['addAccountGeneratedPassword', $domain, $username];
		}
	}
	
	public function setSizePasswordGenerator(string $domain, string $username) : array {
		$this->printDisplayText(
			'Set size of the password',
			'Please enter the size you want: '
		);
		
		$input = readline("Size: ");
		if (ctype_digit($input)) {
			$size = intval($input);
			if ($size > 0) {
				return ['setUseSpecialCharacterPasswordGenerator', $domain, $username,  $size];
			}
		}
		
		return ['setSizePasswordGenerator', $domain, $username];
	}
	
	public function setUseSpecialCharacterPasswordGenerator(string $domain, string $username, int $size) : array {
		$this->printConfirmation(
			'Do you want your password to include special characters?',
			['No', 'Yes']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				$pass = $this->generatePassword($size, false);
				return ['confirmAddAccountWithNewPassword', $domain, $username, $pass];
			case '1':
				$pass = $this->generatePassword($size, true);
				return ['confirmAddAccountWithNewPassword', $domain, $username, $pass];
			default:
				return ['setUseSpecialCharacterPasswordGenerator', $domain, $username, $size];
		}
	}
	
	
	public function confirmAddAccount(string $domain, string $username, string $password) : array {
		$this->printConfirmation(
			"Are you sure you want to add the following account?\n\nDomain: $domain\nUsername: $username\nPassword: $password",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch($input) {
			case '0':
				return ["manageAccounts"];
			case '1':
				$this->accountService->addAccount($this->user, $domain, $username, $password);
				return ["manageAccounts"];
			case '2':
				return ["addAccount"];
			default:
				return ["confirmAddAccount", $domain, $username, $password];
		}
	}
    
    public function confirmAddAccountWithNewPassword(string $domain, string $username, string $password) : array {
		$this->printConfirmation(
			"Are you sure you want to add the following account?\n\nDomain: $domain\nUsername: $username\nPassword: $password",
			['Cancel', 'Yes', 'No', 'Generate new password']
		);
		
		$input = readline("Select: ");
		switch($input) {
			case '0':
				return ["manageAccounts"];
			case '1':
				$this->accountService->addAccount($this->user, $domain, $username, $password);
				return ["manageAccounts"];
			case '2':
				return ["addAccountWithNewPassword"];
            case '3':
                return ["addAccountGeneratedPassword", $domain, $username];
			default:
				return ["confirmAddAccountWithNewPassword", $domain, $username, $password];
		}
	}
	
	public function getAccountsByDomain() : array {
		$this->printDisplayText(
			"Get accounts by domain",
			"Please enter the details bellow (tip: you can press enter to have no filter)"
		);
		
		
		$input = readline("Domain: ");
		$accounts = $this->accountService->getAccountsByDomain($this->user, $input);
		return [Window::WINDOW_MOVE, new ListAccountsWindow($this->user, $accounts)];
	}
	
    public function backToManageUser() : array {
        return [Window::WINDOW_MOVE, new ManageUserWindow($this->user)];
    }
}