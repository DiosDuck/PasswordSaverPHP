#Password saver done in PHP

> **_NOTE:_** This project was created in PHP8.1

##What does it do?

It's a console app which saves accounts. If this app is shared with another person, they can use it too freely without accessing your personal account.

The app is very basic in this current state, having basic properties for both the users and the accounts they want to use.

##Requirements

Except for PHP 8.1, we require `openssl` extension for encryption (else we suggest to use the not encrypted variations) and `pdo_sqlite` for using the database (else we suggest to use the in memory or file variations)

##Instalation

Download the code from GitHub and you should be able to use it by executing the `main.php` file from the directory.

##Usage and customization

In `main.php` file there are 3 big classes: UI, Service and Repository.

If you want to change the method of uses, here is a short sum up:
- **In memory usage**: to use the in memory usage **not encrypted**, use the **UserRepository** and **AccountRepository** classes without any parameters, any service can be used.
- **In file usage**: to save the accounts in files **encrypted or not**, use the **FileUserRepository** (or **FileCryptedUserRepository**) and **FileAccountRepository** (or **FileCryptedAccountRepository**) to save the data in files, any service can be used. You need to specify as a path the user file path and the account folder for each user.
- **In DB usage (Default)**: to save the accounts in DB **only encrypted**, use **DBCryptedUserRepository** and **DBCryptedAccountRepository**, any service can be used. You need to specify as a path the DB path, which **both user and accounts** are using.
- **Without timeout**: to have user be able to timeout, use **UserService** and **AccountService** without any parameters, any repository can be user.
- **Timeout (Default)**: to have user be able to timeout, use **TimeoutUserService** and **TimeoutAccountService** without any parameters, any repository can be user.
