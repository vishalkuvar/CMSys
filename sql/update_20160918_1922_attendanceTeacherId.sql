-- 18092016
-- Add Teacher ID into Attendance

ALTER TABLE `attendance_main` ADD `teacher_id` INT(11) NOT NULL AFTER `id`;