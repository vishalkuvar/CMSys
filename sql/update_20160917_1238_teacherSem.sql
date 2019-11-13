-- 20160917
-- Table to record the semesters in which teacher can teach.

CREATE TABLE `teacher_semesters` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
	`teacher_id` INT NOT NULL COMMENT 'Teacher ID',
	`semester` VARCHAR(32) NOT NULL COMMENT 'Semester Name',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
