-- 20160908
-- Extra User Details

--
-- Table structure for table `user_details`
--
CREATE TABLE `user_details` (
	`userId` INT(11) NOT NULL,
	`address` VARCHAR(512) NOT NULL,
	`pincode` INT(11) NOT NULL,
	`mobile_no` VARCHAR(14) NOT NULL,
	`address2` VARCHAR(512) NOT NULL,
	`pincode2` INT(11) NOT NULL,
	`mobile_no2` VARCHAR(14) NOT NULL,
	`sex` CHAR(1) NOT NULL DEFAULT 'M',
	`date` VARCHAR(11) NOT NULL DEFAULT '01-01-2001',
	`birth_place` VARCHAR(128) NOT NULL,
	`religion` VARCHAR(64) NOT NULL,
	`caste` VARCHAR(64) NOT NULL DEFAULT '',
	`category` VARCHAR(64) NOT NULL,
	`blood_group` VARCHAR(8) NOT NULL  DEFAULT '',
	PRIMARY KEY (`userId`)
) ENGINE = InnoDB;