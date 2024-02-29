<?php
namespace UI;

use Entity\IEntity\IUser;
use Service\IService\IUserService;
use Service\IService\IAccountService;
use Utility\AccountListDTO;
use Utility\GeneratePasswordTrait;
use Exception\Authentification\AuthentificationException;
use Exception\Timeout\TimeoutException;
use Exception\Account\AccountException;

class UI {
	private IUserService $userService;
	private IAccountService $accountService;
	private ?IUser $user;
	
	use GeneratePasswordTrait;
	
	public function __construct(IUserService $userService, IAccountService $accountService) {
		$this->userService = $userService;
		$this->accountService = $accountService;
		$this->user = null;
	}
	
	public function run(){
		$this->clearConsole();
		$output = $this->callMethod(['welcomePage']);
		while (true) {
			try {
				$this->clearConsole();
				if ($output[0] == 'exit') 
					return;
				$output = $this->callMethod($output);
			}
			catch (AuthentificationException|TimeoutException $e) {
				$this->clearConsole();
				$this->printErrorMessage($e);
				$this->user = null;
				$output = $this->callMethod(['welcomePage']);
			}
			catch (AccountException $e) {
				$this->clearConsole();
				$this->printErrorMessage($e);
				$output = $this->callMethod(['manageAccounts']);
			}
		}
	}
	
	private function welcomePage() : array {
		return $this->user ? $this->welcomePageLogged() : $this->welcomePageNotLogged();
	}
	
	private function welcomePageNotLogged() : array {
		$this->printDisplayText(
			"Welcome!",
			"Use the numbers to select your next command",
			["Login", "Create new user"]
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['exit'];
			case '1':
				return ['login'];
			case '2':
				return ['register'];
			default:
				return ['welcomePage'];
		}
	}
	
	private function welcomePageLogged() : array {
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
	
	private function changePassword() : array {
		$this->printDisplayText(
			"Change " . $this->user->getName() .  "'s password",
			"Please enter the details down bellow:"
		);
		
		$oldPassword = $this->whileNotEmptyInput("Old Password: ");
		$newPassword = $this->whileNotEmptyInput("New Password: ");
		
		return ['confirmChangePassword', $oldPassword, $newPassword];
	}
	
	private function confirmChangePassword(string $oldPassword, string $newPassword) : array {
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
	
	private function deleteUser() : array {
		$this->printDisplayText(
			"Delete " . $this->user->getName(),
			"Please enter the password down bellow:"
		);
		
		$password = $this->whileNotEmptyInput("Password :");
		return ['confirmDeleteUser', $password];
	}
	
	private function confirmDeleteUser(string $password) : array {
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
				$this->user = null;
				return ['welcomePage'];
			default:
				return ['confirmDeleteUser', $password];
		}
	}
	
	private function changeName() : array {
		$this->printDisplayText(
			"Change " . $this->user->getName() .  "'s name",
			"Please enter the details down bellow:"
		);
		
		$password = $this->whileNotEmptyInput("Password: ");
		$newName = $this->whileNotEmptyInput("New name: ");
		
		return ['confirmChangeName', $password, $newName];
	}
	
	private function confirmChangeName(string $password, string $newName) : array {
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
	
	private function login() : array {	
		$this->printDisplayText(
			"Login",
			"Please enter the details down bellow:"
		);
		
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		$this->user = $this->userService->logIn($username, $password);
		
		return ['welcomePage'];
	}
	
	private function logout() : array {
		$this->user = null;
		return ['welcomePage'];
	}
	
	private function register() : array {	
		$this->printDisplayText(
			"Register",
			"Please enter the details down bellow:"
		);
		
		$name = $this->whileNotEmptyInput("Name: ");
		$username = $this->whileNotEmptyInput("Username (must be unique): ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		return ['confirmCreateUser', $name, $username, $password];
	}
	
	private function confirmCreateUser(string $name, string $username, string $password) : array {
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
	
	private function manageAccounts() : array {
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
				return ['exit'];
			case '1':
				return ['addAccount'];
			case '2':
				return ['addAccountWithNewPassword'];
			case '3':
				return ['getAccountsByDomain'];
			case '4':
				return ['welcomePage'];
			default:
				return ['manageAccounts'];
		}
	}
	
	private function addAccount() : array {
		$this->printDisplayText(
			"Add new account",
			"Please enter the details bellow"
		);
		
		$domain = $this->whileNotEmptyInput("Domain (make it unique): ");
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->whileNotEmptyInput("Password: ");
		
		return ['confirmAddAddount', $domain, $username, $password, false];
	}
	
	private function addAccountWithNewPassword() : array {
		$this->printDisplayText(
			"Add new account and generate a new password",
			"Please enter the details bellow"
		);
		
		$domain = $this->whileNotEmptyInput("Domain (make it unique): ");
		$username = $this->whileNotEmptyInput("Username: ");
		$password = $this->generatePassword();
		
		return ['confirmAddAddount', $domain, $username, $password, true];
	}
	
	private function confirmAddAddount(string $domain, string $username, string $password, bool $isGenerated) : array {
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
				return $isGenerated ? ['addAccountWithNewPassword'] : ["addAccount"];
			default:
				return ["confirmAddAddount", $domain, $username, $password, $isGenerated];
		}
	}
	
	private function getAccountsByDomain() : array {
		$this->printDisplayText(
			"Get accounts by domain",
			"Please enter the details bellow (tip: you can press enter to have no filter)"
		);
		
		
		$input = readline("Domain: ");
		$accounts = $this->accountService->getAccountsByDomain($this->user, $input);
		return ['accountsDisplayPage', $accounts];
	}
	
	private function accountsDisplayPage(AccountListDTO $accounts) : array {
		$this->printAccounts(
			"Accounts found!",
			$accounts,
			"Choose one of the options down bellow:",
			[
				"Update password manually for account", 
				"Generate new password for account", 
				"Delete account", 
				"Return"
			]
		);
		
		$input = readline("Select: ");
		switch($input) {
			case '0':
				return ['exit'];
			case '1':
				return ['updateAccountPassword', $accounts, false];
			case '2':
				return ['updateAccountPassword', $accounts, true];
			case '3':
				return ['deleteAccountChoose', $accounts];
			case '4':
				return ['manageAccounts'];
			default:
				return ['accountsDisplayPage', $accounts];
		}
	}
	
	private function deleteAccountChoose(AccountListDTO $accounts) : array {
		$this->printAccounts(
			"Delete Account",
			$accounts,
			"Select the account you want to delete (pressing only enter will return back to the menu)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$domain = $accounts->getAccountDomainById(intval($input));
			if ($domain != null) {
				return ['confirmDeleteAccount', $accounts, $domain];
			}
		}
		else if ($input == '') {
			return ['accountsDisplayPage', $accounts];
		}
		return ['deleteAccountChoose', $accounts];
	}
	
	private function confirmDeleteAccount(AccountListDTO $accounts, string $domain) : array {
		$this->printConfirmation(
			"Are you sure you want to delete the account with domain '$domain'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['manageAccounts'];
			case '1':
				$this->accountService->deleteAccount($this->user, $domain);
				return ['manageAccounts'];
			case '2':
				return ['deleteAccountChoose', $accounts];
			default:
				return ['confirmDeleteAccount', $accounts, $domain];
		}
	}
	
	private function updateAccountPassword(AccountListDTO $accounts, bool $isGenerated) : array {
		$this->printAccounts(
			"Update Password for Account",
			$accounts,
			"Select the account you want to update (pressing only enter will return back to the menu)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$domain = $accounts->getAccountDomainById(intval($input));
			if ($domain != null) {
				return $isGenerated ? ['updateAccountGeneratedPassword', $domain] : ['insertAccountNewPassword', $domain];
			}
		}
		else if ($input == '') {
			return ['accountsDisplayPage', $accounts];
		}
		return ['updateAccountPassword', $accounts];
	}
	
	private function updateAccountGeneratedPassword(string $domain) : array {
		$this->printConfirmation(
			'Do you want to customize the password generator? (the default lenght is 16 and incluse special characters)',
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['manageAccounts'];
			case '1':
				return ['setSizePasswordGenerator', $domain];
			case '2':
				$password = $this->generatePassword();
				return ['confirmAccountNewPassword', $domain, $password, true];
			default:
				return ['updateAccountGeneratedPassword', $domain];
		}
	}
	
	private function setSizePasswordGenerator(string $domain) : array {
		$this->printDisplayText(
			'Set size of the password',
			'Please enter the size you want: '
		);
		
		$input = readline("Size: ");
		if (ctype_digit($input)) {
			$size = intval($input);
			if ($size > 0) {
				return ['setUseSpecialCharacterPasswordGenerator', $domain, $size];
			}
		}
		
		return ['setSizePasswordGenerator', $domain];
	}
	
	private function setUseSpecialCharacterPasswordGenerator(string $domain, int $size) : array {
		$this->printConfirmation(
			'Do you want your password to include special characters?',
			['No', 'Yes']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				$pass = $this->generatePassword($size, false);
				return ['confirmAccountNewPassword', $domain, $pass, true];
			case '1':
				$pass = $this->generatePassword($size, true);
				return ['confirmAccountNewPassword', $domain, $pass, true];
			default:
				return ['setUseSpecialCharacterPasswordGenerator', $domain, $size];
		}
	}
	
	private function insertAccountNewPassword(string $domain) : array {
		$this->printDisplayText(
			"Change password for account",
			"Enter the new password for the domain '$domain'"
		);
		
		$input = $this->whileNotEmptyInput("New password: ");
		return ['confirmAccountNewPassword', $domain, $input, false];
	}
	
	private function confirmAccountNewPassword(string $domain, string $password, bool $isGenerated = false) : array {
		$this->printConfirmation(
			"Are you sure you want the password '$password' for the domain '$domain'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['manageAccounts'];
			case '1':
				$this->accountService->updateAccountPassword($this->user, $domain, $password);
				return ['manageAccounts'];
			case '2':
				return $isGenerated ? ['updateAccountGeneratedPassword', $domain] : ['insertAccountNewPassword', $domain];
			default:
				return ['confirmAccountNewPassword', $domain, $password, $isGenerated];
		}
	}
	
	private function callMethod(array $parameters): array {
        $method = array_shift($parameters);
        return call_user_func_array([$this, $method], $parameters);
    }

	private function clearConsole(): void {
        //TODO: find a cleaner solution
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    private function printDisplayText(string $title, string $subtitle = '', array $list = [], string $notes = ''): void {
        $text = "\e[1;32m$title\e[0m\n";
        if ($subtitle)
            $text .= "\n\e[1m$subtitle\e[0m\n";
        if ($notes)
            $text .= "\n\e[2;3mNote: $notes\e[0m\n";
        if ($list) {
            for ($i = 0; $i < count($list); $i++) {
                $text .= "\n" . ($i + 1) . ": " . $list[$i];
            }
            $text .= "\n0: Exit\n";
        }
		echo $text . PHP_EOL;
    }

    private function printConfirmation(string $title, array $list): void {
        $text = "\e[1m$title\e[0m\n\n";
        for ($i = 1; $i < count($list); $i++) {
            $text .= "" . $i . ": " . $list[$i] . str_repeat(' ', 5);
        }
        echo $text . "0: " . $list[0] . "\n\n";
    }
	
	private function whileNotEmptyInput($text) : string {
		$input = readline($text);
		while (!$input) {
			$input = readline($text);
		}
		
		return $input;
	}
	
	private function printErrorMessage($ex) : void {
		echo "\e[1;91m$ex\e[0m\n\n";
	}
	
	private function printAccounts(string $title, AccountListDTO $accounts, string $subtitle = '', array $list = []) : void {
		$data = $accounts->getAllAccounts();
        $columnLengths = [
            strlen('Number'),
            strlen('Domain'),
			strlen('User/Email'),
            strlen('Password')
        ];

        foreach ($data as $row) {
            foreach ($row as $index => $value) {
                $length = strlen($value);
                if ($length > $columnLengths[$index + 1]) {
                    $columnLengths[$index + 1] = $length;
                }
            }
        }

        $lineLength = array_sum($columnLengths) + 3 * count($columnLengths) + 1;
        $table = str_repeat('*', $lineLength)
            . "\n"
            . '* ' . str_pad('Number', $columnLengths[0])
            . ' * ' . str_pad('Domain', $columnLengths[1])
            . ' * ' . str_pad('User/Email', $columnLengths[2])
            . ' * ' . str_pad('Password', $columnLengths[3])
            . " *\n"
            . str_repeat('*', $lineLength)
            . "\n";

        foreach ($data as $counter => $row) {
            $table .=  '* ' . str_pad($counter, $columnLengths[0])
                . ' * ' . str_pad($row[0], $columnLengths[1])
                . ' * ' . str_pad($row[1], $columnLengths[2])
                . ' * ' . str_pad($row[2], $columnLengths[3])
                . " *\n";
        }

        $table .= str_repeat('*', $lineLength) . "\n";
        
        $text = "\e[1;32m$title\e[0m\n";
        if ($subtitle)
            $text .= "\n\e[1m$subtitle\e[0m\n";
        $text .= "\n\e[1m$table\e[0m\n";
        if ($list) {
            for ($i = 0; $i < count($list); $i++) {
                $text .= "\n" . ($i + 1) . ": " . $list[$i];
            }
            $text .= "\n0: Exit\n";
        }
		echo $text . PHP_EOL;
	}
}