-- Remove old table
DROP TABLE IF EXISTS `messages`;

-- Create new table
CREATE TABLE `messages` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`from` INT NOT NULL,
	`to` INT NOT NULL,
	`content` VARCHAR(255) NOT NULL,
	`sent_on` INT NOT NULL,
	`read_on` INT
);

INSERT INTO `messages` (`from`, `to`, `content`, `sent_on`) 
VALUES (2, 1, 'Testing message 123.', UNIX_TIMESTAMP());
