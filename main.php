<?php
include 'autoloader.php';

error_reporting(E_ALL);
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set("error_log", __DIR__ . "/error.log");

try {
	/*
	#create folders/files
	#comment if not needed
	$fileDir = __DIR__ . '/users/';
	if (!file_exists($fileDir)) {
		mkdir($fileDir);
	}
	$userFile = $fileDir . 'users.txt';

	$accountDir = $fileDir . 'accounts/';
	if (!file_exists($accountDir)) {
		mkdir($accountDir);
	}
	*/

	#create database and connect to it
	#comment if not needed
	$dbDir = __DIR__ . '/db/';
	if (!file_exists($dbDir)) {
		mkdir($dbDir);
	}
	try {
		$pdoConnection = new PDO('sqlite:' . $dbDir . 'app.db');
	} catch (PDOException $e) {
		throw new Exception\DB\DBException($e);
	}

	#create builders
	$userBuilder = new Builder\CryptedUserBuilder();
	$accountBuilder = new Builder\CryptedAccountBuilder();

	#create mappers
	$sqlQuery = new SqlQuery\SqlQuery();
	$userMapper = new Mapper\DB\CryptedUserDBMapper($sqlQuery);
	$accountMapper = new Mapper\DB\CryptedAccountDBMapper($sqlQuery);

	#create repository
	$repoUser = new Repository\DbUserRepository($pdoConnection, $userMapper);
	$repoAccount = new Repository\DbAccountRepository($pdoConnection, $accountMapper);
	
	#create service
	$userService = new Service\TimeoutUserService($repoUser, $userBuilder);
	$accountService = new Service\TimeoutAccountService($repoAccount, $accountBuilder);
	
	#start app
	$ui = new UI\NewUI($userService, $accountService);
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