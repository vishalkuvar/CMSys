-- 02092016
-- Adds Year and Branch into login table.
ALTER TABLE `login` ADD `year` INT NOT NULL AFTER `title`, ADD `branch` VARCHAR(255) NOT NULL AFTER `year`;
-- Add's Profile Picture
ALTER TABLE `login` ADD `picture` BLOB NULL AFTER `branch`;