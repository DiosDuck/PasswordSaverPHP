<?php
include 'autoloader.php';

error_reporting(E_ALL);
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set("error_log", __DIR__ . "/error.log");

try {
	$dbDir = __DIR__ . '/db/';
	if (!file_exists($dbDir)) {
		mkdir($dbDir);
	}
	try {
		$pdoConnection = new PDO('sqlite:' . $dbDir . 'app.db');
	} catch (PDOException $e) {
		throw new Exception\DB\DBException($e);
	}
	$repoService = new Repository\DbCryptedUserRepository($pdoConnection);
	$repoAccount = new Repository\DbCryptedAccountRepository($pdoConnection);
	$userService = new Service\TimeoutUserService($repoService);
	$accountService = new Service\TimeoutAccountService($repoAccount);
	$ui = new UI\UI($userService, $accountService);
	$ui->run();
} catch (Exception\DB\DBException $e) {
    echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
	echo "\e[1;91m$e\e[0m\n\n";
	error_log($e->getException());
} catch (Exception\Type\TypeException $e) {
    echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
	echo "\e[1;91m$e\e[0m\n\n";
	error_log($e->__toString() . PHP_EOL . $e->getTraceAsString());
}