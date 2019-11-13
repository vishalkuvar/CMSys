-- 10092016
-- Adds paper_student
-- Describes which student can see which paper.

CREATE TABLE `paper_student` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`paper_id` INT NOT NULL COMMENT 'Paper ID',
	`student_id` INT NOT NULL COMMENT 'Student Id',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
