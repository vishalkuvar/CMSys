-- 10092016
-- Adds checked and uploaded field in paper_student
-- Helps to determine if paper was already chcked/uploaded.

ALTER TABLE `paper_student` ADD `uploaded` TINYINT NOT NULL DEFAULT '0' AFTER `student_id`;
ALTER TABLE `paper_student` ADD `checked` TINYINT NOT NULL DEFAULT '0' AFTER `uploaded`;
