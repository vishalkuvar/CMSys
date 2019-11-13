-- 10092016
-- Saving Paper Log(To Recreate the marks/text)

CREATE TABLE `paper_clog` (
	`student_id` INT(11) NOT NULL,
	`paper_id` INT(11) NOT NULL,
	`page_no` INT(11) NOT NULL,
	`x` VARCHAR(512) NOT NULL,
	`y` VARCHAR(512) NOT NULL,
	`text` VARCHAR(512) NOT NULL,
	`color` VARCHAR(1024) NOT NULL,
	`count` INT(11) NOT NULL,
	PRIMARY KEY (`student_id`, `paper_id`)
) ENGINE = InnoDB;
