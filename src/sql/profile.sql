-- Remove old table
DROP TABLE IF EXISTS `profile`;

-- Create new table
CREATE TABLE `profile` (
	`user_id` INT,
	`attribute` VARCHAR(255),
	`value` VARCHAR(255) NOT NULL,
	`timestamp` INT NOT NULL,
	PRIMARY KEY (`user_id`, `attribute`)
);

INSERT INTO `profile` (`user_id`, `attribute`, `value`, `timestamp`) 
VALUES (1, 'title', 'Pornographic Actress at Jules Jordan Network', UNIX_TIMESTAMP()),
(1, 'aliases', 'Molly, Paige Riley, Riley Reid, Riley Ried', UNIX_TIMESTAMP()),
(1, 'website', 'rileyreid.com', UNIX_TIMESTAMP()),
(1, 'twitter', 'RileyReidx3', UNIX_TIMESTAMP()),
(1, 'location', 'California', UNIX_TIMESTAMP()),
(1, 'dob', 'July 9th, 1991', UNIX_TIMESTAMP()),
(1, 'debut', '2011', UNIX_TIMESTAMP()),
(1, 'measurements', '30B-24-35', UNIX_TIMESTAMP()),
(1, 'height', '5\'3', UNIX_TIMESTAMP()),
(1, 'eyecolor', 'Green', UNIX_TIMESTAMP()),
(1, 'haircolor', 'Brunette', UNIX_TIMESTAMP()),
(1, 'race', 'Caucasian, Hispanic', UNIX_TIMESTAMP()),
(1, 'ethnicity', 'American, Irish, Dominican, Puerto Rican', UNIX_TIMESTAMP());
