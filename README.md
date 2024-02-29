# Password saver done in PHP

> **_NOTE:_** This project was created in PHP8.1

## What does it do?

It's a console app which saves accounts. If this app is shared with another person, they can use it too freely without accessing your personal account.

The app is very basic in this current state, having basic properties for both the users and the accounts they want to use.

## Requirements

Except for PHP 8.1, we require `openssl` extension for encryption (else we suggest to use the not encrypted variations) and `pdo_sqlite` for using the database (else we suggest to use the in memory or file variations)

## Instalation

Download the code from GitHub and you should be able to use it by executing the `main.php` file from the directory.

## Usage and customization

In `main.php` file there are 5 big group of classes: UI, Service, Repository, Builder and Mapper.

### UI
**UI** is the main class which focus is to display the data to the user throw the console. It contains a trait to help generating password (for the option to generate them automatically) and the services for user management and their accounts.

### Service
**Service** represents the tie between UI and the rest of the code, changing the data received from the user in code data. It contains two classes: a repository and a builder. As of the customization, there are two stuff:
- **Classic** which does the basic stuff of connecting the UI with the rest of the code
- **Timeout (default)** which adds an extra future of timeout which lasts 5 minutes since the last change of data

### Builder
**Builder** represents the format of the class the user want to, takes the input data and converts to the class the user wants. There are two type of objects:
- **Classic** which the data corresponds with the input of the user
- **Crypted (default)** which encrypt and decrypts the password corresponded with a specific key generated at runtime, it's suggested to be used for data saving

### Repository
**Repository** represents the data collector which makes the CRUD operations on the data. Each repository contains different data, depending of the mod they want to use it:
- **Classic** which is the in memory way to save the data. On closing the app, the data will be lost.
- **File** which uses files to read and write data. The data will be saved in those files and depending of the Mapper either can be classic or encrypted
- **DataBase (default)** which uses the database to stock the data. As for the File Repository, it can be either classic or encrypted

### Mapper
**Mapper** is used on File and Database data. Their purpose is to convert string into objects, while also automatically using the specific data the User want to
> **_NOTE:_** the Mapper should use the same type as the Builder uses, or else there will be an error thrown!

## Conculsion

Thanks for taking your time to read this and hope the code was useful to you :)

