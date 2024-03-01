<?php

namespace UI\Window;

use Service\IService\IAccountService;
use Service\IService\IUserService;

abstract class Window {
    const WINDOW_EXIT = "exit";
    const WINDOW_MOVE = "move"; 
    public abstract function setUserService(IUserService $userService) : void;
    
    public abstract function setAccountService(IAccountService $accountService) : void;

    public abstract function run() : ?Window;

    protected function callMethod(array $parameters): array {
        $method = array_shift($parameters);
        return call_user_func_array([$this, $method], $parameters);
    }

	protected function clearConsole(): void {
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    protected function printDisplayText(string $title, string $subtitle = '', array $list = [], string $notes = ''): void {
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

    protected function printConfirmation(string $title, array $list): void {
        $text = "\e[1m$title\e[0m\n\n";
        for ($i = 1; $i < count($list); $i++) {
            $text .= "" . $i . ": " . $list[$i] . str_repeat(' ', 5);
        }
        echo $text . "0: " . $list[0] . "\n\n";
    }
	
	protected function whileNotEmptyInput($text) : string {
		$input = readline($text);
		while (!$input) {
			$input = readline($text);
		}
		
		return $input;
	}
	
	protected function printErrorMessage($ex) : void {
		echo "\e[1;91m$ex\e[0m\n\n";
	}
}