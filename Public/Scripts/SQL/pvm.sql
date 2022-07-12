-- Creating the database
CREATE DATABASE PasswordManager;
-- Creating the User's table
CREATE TABLE PasswordManager.Users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(32),
    lastName VARCHAR(32),
    mailAddress VARCHAR(64),
    encryptedPassword VARCHAR(128)
);