-- 12092016
-- Add's a Checked By Field for paper.

ALTER TABLE `paper_student` ADD `checked_by` INT(12) NOT NULL AFTER `checked`;
