-- 10092016
-- Adds Marks Obtained

ALTER TABLE `paper_student` ADD `marks` INT NOT NULL DEFAULT '0' AFTER `checked`;
