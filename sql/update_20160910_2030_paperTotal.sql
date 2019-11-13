-- 10092016
-- Adds Total Marks

ALTER TABLE `paper_main` ADD `marks` INT NOT NULL DEFAULT '0' AFTER `subject_code`;
