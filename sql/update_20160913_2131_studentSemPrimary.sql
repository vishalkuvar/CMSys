-- 13092016
-- Change Primary Key

ALTER TABLE `student_semesters` ADD UNIQUE(`id`);
ALTER TABLE `student_semesters` DROP PRIMARY KEY, ADD PRIMARY KEY(`user_id`, `semester`);
