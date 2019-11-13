-- 18092016
-- Attendance Tables

CREATE TABLE `attendance_main` (
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Attendance Id',
	`topics` VARCHAR(512) NOT NULL COMMENT 'Topics Taught',
	`semester` VARCHAR(128) NOT NULL COMMENT 'Semester Name',
	`branch` VARCHAR(128) NOT NULL COMMENT 'Branch Name',
	`subject_code` VARCHAR(12) NOT NULL COMMENT 'Subject Code',
	`attendance` TINYINT(1) NOT NULL COMMENT 'Total Attendance of the Day',
	`date` VARCHAR(12) NOT NULL COMMENT 'Date of Attendance',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `attendance_student` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT 'Student Attendance ID',
	`attendance_id` INT NOT NULL COMMENT 'Attendance Id',
	`student_id` INT NOT NULL COMMENT 'Student Id',
	`attended` TINYINT(1) NOT NULL COMMENT 'Attended or not',
	PRIMARY KEY (`id`),
	UNIQUE `student_attendance` (`attendance_id`, `student_id`)
) ENGINE = InnoDB;
