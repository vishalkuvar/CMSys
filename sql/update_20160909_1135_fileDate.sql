-- 20160909
-- Add Upload Date in `files` table

ALTER TABLE `files` ADD `date` INT(12) NOT NULL COMMENT 'Upload Date' AFTER `internal_filename`;
