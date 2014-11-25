-- Remove old table
DROP TABLE IF EXISTS `users`;

-- Create new table
CREATE TABLE `users` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`brand` VARCHAR(255) UNIQUE,
	`email` VARCHAR(255) NOT NULL UNIQUE,
	`first_name` VARCHAR(255) NOT NULL,
	`last_name` VARCHAR(255) NOT NULL,
	`password` CHAR(40) NOT NULL # SHA-1 hash length
);

INSERT INTO `users` (`email`, `first_name`, `last_name`, `password`) 
VALUES ('john@doe.com', 'John', 'Doe', SHA('test123')),
	('mark@twain.com', 'Mark', 'Twain', SHA('password123')),
	('james@deen.com', 'James', 'Deen', SHA('test123'));
	