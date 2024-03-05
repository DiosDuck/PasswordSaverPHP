<?php

namespace UI\Window;

use Entity\IEntity\IUser;
use Service\IService\IAccountService;
use Service\IService\IUserService;
use Utility\GeneratePasswordTrait;
use Exception\Timeout\TimeoutException;
use Exception\Account\AccountException;
use Utility\AccountListDTO;

class ListAccountsWindow extends Window {
    private IUser $user;
    private IAccountService $accountService;
    private AccountListDTO $accountList;
	
	use GeneratePasswordTrait;

    public function __construct(IUser $user, AccountListDTO $accountList) {
        $this->user = $user;
        $this->accountList = $accountList;
    }

    public function setUserService(IUserService $userService) : void {
        //do nothing
    }

    public function setAccountService(IAccountService $accountService) : void {
        $this->accountService = $accountService;
    }

    public function run() : ?Window {
        $this->clearConsole();
		$output = $this->callMethod(['accountsDisplayPage']);
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
				$output = $this->callMethod(['accountsDisplayPage']);
            }
		}
    }

    public function backToManageAccount() : array {
        return [Window::WINDOW_MOVE, new ManageAccountWindow($this->user)];
    }

    public function accountsDisplayPage() : array {
		$this->printAccounts(
			"Accounts found!",
			"Choose one of the options down bellow:",
			[
				"Show password",
				"Hide password",
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
				return ['showPassword'];
			case '2':
				return ['hidePassword'];
			case '3':
				return ['updateAccountPassword', false];
			case '4':
				return ['updateAccountPassword', true];
			case '5':
				return ['deleteAccountChoose'];
			case '6':
				return ['backToManageAccount'];
			default:
				return ['accountsDisplayPage'];
		}
	}
	
	public function showPassword() : array {
		$this->printAccounts(
			"Show password",
			"Select the account you want to make it visible (typing something else will just return back to the list)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$this->accountList->showPassword(intval($input));
		}
		return ['accountsDisplayPage'];
	}
	public function hidePassword() : array {
		$this->printAccounts(
			"Hide password",
			"Select the account you want to hide (typing something else will just return back to the list)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$this->accountList->hidePassword(intval($input));
		}
		return ['accountsDisplayPage'];
	}

	public function deleteAccountChoose() : array {
		$this->printAccounts(
			"Delete Account",
			"Select the account you want to delete (pressing only enter will return back to the menu)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$domain = $this->accountList->getAccountDomainById(intval($input));
			if ($domain != null) {
				return ['confirmDeleteAccount', $domain];
			}
		}
		else if ($input == '') {
			return ['accountsDisplayPage'];
		}
		return ['deleteAccountChoose'];
	}
	
	public function confirmDeleteAccount(string $domain) : array {
		$this->printConfirmation(
			"Are you sure you want to delete the account with domain '$domain'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['accountsDisplayPage'];
			case '1':
				$this->accountService->deleteAccount($this->user, $domain);
				return ['backToManageAccount'];
			case '2':
				return ['deleteAccountChoose'];
			default:
				return ['confirmDeleteAccount', $domain];
		}
	}
	
	public function updateAccountPassword(bool $isGenerated) : array {
		$this->printAccounts(
			"Update Password for Account",
			"Select the account you want to update (pressing only enter will return back to the menu)"
		);

		$input = readline("Select: ");
		if (ctype_digit($input)) {
			$domain = $this->accountList->getAccountDomainById(intval($input));
			if ($domain != null) {
				return $isGenerated ? ['updateAccountGeneratedPassword', $domain] : ['insertAccountNewPassword', $domain];
			}
		}
		else if ($input == '') {
			return ['accountsDisplayPage'];
		}
		return ['updateAccountPassword', $isGenerated];
	}
	
	public function updateAccountGeneratedPassword(string $domain) : array {
		$this->printConfirmation(
			'Do you want to customize the password generator? (the default lenght is 16 and incluse special characters)',
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['accountsDisplayPage'];
			case '1':
				return ['setSizePasswordGenerator', $domain];
			case '2':
				$password = $this->generatePassword();
				return ['confirmAccountNewPassword', $domain, $password, true];
			default:
				return ['updateAccountGeneratedPassword', $domain];
		}
	}
	
	public function setSizePasswordGenerator(string $domain) : array {
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
	
	public function setUseSpecialCharacterPasswordGenerator(string $domain, int $size) : array {
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
	
	public function insertAccountNewPassword(string $domain) : array {
		$this->printDisplayText(
			"Change password for account",
			"Enter the new password for the domain '$domain'"
		);
		
		$input = $this->whileNotEmptyInput("New password: ");
		return ['confirmAccountNewPassword', $domain, $input, false];
	}
	
	public function confirmAccountNewPassword(string $domain, string $password, bool $isGenerated = false) : array {
		$this->printConfirmation(
			"Are you sure you want the password '$password' for the domain '$domain'?",
			['Cancel', 'Yes', 'No']
		);
		
		$input = readline("Select: ");
		switch ($input) {
			case '0':
				return ['accountsDisplayPage'];
			case '1':
				$this->accountService->updateAccountPassword($this->user, $domain, $password);
				return ['backToManageAccount'];
			case '2':
				return $isGenerated ? ['updateAccountGeneratedPassword', $domain] : ['insertAccountNewPassword', $domain];
			default:
				return ['confirmAccountNewPassword', $domain, $password, $isGenerated];
		}
	}
    
    private function printAccounts(string $title, string $subtitle = '', array $list = []) : void {
		$data = $this->accountList->getAllAccounts();
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