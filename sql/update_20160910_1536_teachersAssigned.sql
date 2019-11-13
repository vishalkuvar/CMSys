-- 10092016
-- Adds Subjects Assigned to Teachers

CREATE TABLE `teacher_subjects` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`teacher_id` INT NOT NULL,
	`branch` VARCHAR(256) NOT NULL,
	`subject_code` VARCHAR(16) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
