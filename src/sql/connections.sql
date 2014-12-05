-- Remove old table
DROP TABLE IF EXISTS `connections`;

-- Create new table
CREATE TABLE `connections` (
	`to_user` INT, # lower id
	`from_user` INT, # higher id
	`status` VARCHAR(255) NOT NULL,
	`timestamp` INT NOT NULL,
	PRIMARY KEY (`to_user`, `from_user`)
);

INSERT INTO `connections` (`to_user`, `from_user`, `status`, `timestamp`) 
VALUES (1, 2, 'accepted', UNIX_TIMESTAMP()),
(1, 3, 'pending', UNIX_TIMESTAMP()),
(1, 4, 'rejected', UNIX_TIMESTAMP());
