-- 10092016
-- Page Number is also a Primary Key.

ALTER TABLE `paper_clog` DROP PRIMARY KEY, ADD PRIMARY KEY(`student_id`, `paper_id`, `page_no`);
