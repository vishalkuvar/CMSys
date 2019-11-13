-- 10092016
-- Adds Paper_main
-- Stores basic information for retrieving papers.

CREATE TABLE `paper_main` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT 'Paper ID',
	`name` VARCHAR(255) NOT NULL COMMENT 'Paper Name',
	`date` VARCHAR(11) NOT NULL COMMENT 'Paper Date',
	`semester` VARCHAR(128) NOT NULL COMMENT 'Sem Name',
	`branch` VARCHAR(128) NOT NULL COMMENT 'Branch Name',
	`subject_code` VARCHAR(12) NOT NULL COMMENT 'Subject Code',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
