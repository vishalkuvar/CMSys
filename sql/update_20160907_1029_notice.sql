-- 20160907
-- Files: Add Uploaded By (Id), FileName

ALTER TABLE `files` ADD `uploaded_by` INT(11) NOT NULL DEFAULT '-1';
ALTER TABLE `files` ADD `internal_filename` VARCHAR(255) NOT NULL;
