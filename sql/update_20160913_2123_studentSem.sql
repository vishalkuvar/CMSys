-- 13092016
-- Records Student Semesters

CREATE TABLE `student_semesters` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`semester` VARCHAR(16) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
