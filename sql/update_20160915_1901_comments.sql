-- 15092016
-- Added Comments on SQL Tables 

-- For `files` table
ALTER TABLE `files` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
					CHANGE `file_type` `file_type` INT(11) NOT NULL DEFAULT '-1' COMMENT 'FileType(Constants in FileHandler)',
					CHANGE `file_name` `file_name` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'File Name to Show',
					CHANGE `branch` `branch` INT(11) NOT NULL DEFAULT '0' COMMENT 'Branch ID',
					CHANGE `semester` `semester` INT(11) NOT NULL DEFAULT '0' COMMENT 'SemesterID',
					CHANGE `uploaded_by` `uploaded_by` INT(11) NOT NULL DEFAULT '-1' COMMENT 'Staff ID',
					CHANGE `internal_filename` `internal_filename` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Internal FileName',
					CHANGE `date` `date` INT(12) NOT NULL COMMENT 'Upload Date';

-- For `log` table
ALTER TABLE `log` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique Log ID',
				 CHANGE `user_id` `user_id` INT(11) NOT NULL COMMENT 'User ID',
				 CHANGE `log_type` `log_type` INT(11) NOT NULL COMMENT 'LogType(LogHandler Constant)',
				 CHANGE `log` `log` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Log Reason',
				 CHANGE `date` `date` INT(11) NOT NULL COMMENT 'Date of Log',
				 CHANGE `ip` `ip` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'User IP';

-- For `login` table
ALTER TABLE `login` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID',
				 CHANGE `user` `user` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Username',
				 CHANGE `password` `password` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Bcrypted Password',
				 CHANGE `email` `email` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Email',
				 CHANGE `name` `name` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Name',
				 CHANGE `verified` `verified` INT(11) NOT NULL DEFAULT '0' COMMENT 'Email Verified?',
				 CHANGE `title` `title` INT(11) NOT NULL DEFAULT '1' COMMENT 'Title',
				 CHANGE `semester` `semester` VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Sem0' COMMENT 'Semester Name',
				 CHANGE `branch` `branch` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Branch Name',
				 CHANGE `picture` `picture` BLOB NULL DEFAULT NULL COMMENT 'NOT USED',
				 CHANGE `ip` `ip` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0.0.0.0' COMMENT 'Last IP';

-- For `paper_clog` table
ALTER TABLE `paper_clog` CHANGE `student_id` `student_id` INT(11) NOT NULL COMMENT 'Student Id',
						 CHANGE `paper_id` `paper_id` INT(11) NOT NULL COMMENT 'Paper Id',
						 CHANGE `page_no` `page_no` INT(11) NOT NULL COMMENT 'Page Number',
						 CHANGE `x` `x` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Serialized x co-ord',
						 CHANGE `y` `y` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Serialized y co-ord',
						 CHANGE `text` `text` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Serialized marks',
						 CHANGE `color` `color` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Serialized color',
						 CHANGE `count` `count` INT(11) NOT NULL COMMENT 'Number of x co-ords';

-- For `paper_main` table
ALTER TABLE `paper_main` CHANGE `marks` `marks` INT(11) NOT NULL DEFAULT '0' COMMENT 'Total Marks';

-- For `paper_student` table
ALTER TABLE `paper_student` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique Paper ID',
						 CHANGE `paper_id` `paper_id` INT(11) NOT NULL COMMENT 'Paper ID',
						 CHANGE `student_id` `student_id` INT(255) NOT NULL COMMENT 'Paper Name',
						 CHANGE `uploaded` `uploaded` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Upload Status',
						 CHANGE `checked` `checked` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Check Status',
						 CHANGE `checked_by` `checked_by` INT(12) NOT NULL COMMENT 'Teacher ID',
						 CHANGE `marks` `marks` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Marks per page(serialized)',
						 CHANGE `tmarks` `tmarks` INT(11) NOT NULL DEFAULT '0' COMMENT 'Total Marks Obtained';

-- For `sql_version` table
ALTER TABLE `sql_version` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique SQLVersion ID',
						CHANGE `name` `name` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'SQL File Name';

-- For `student_semester` table
ALTER TABLE `student_semesters` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
							 CHANGE `user_id` `user_id` INT(11) NOT NULL COMMENT 'User ID',
							 CHANGE `semester` `semester` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Semester Name';

-- For `teacher_subjects` table
ALTER TABLE `teacher_subjects` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
							 CHANGE `teacher_id` `teacher_id` INT(11) NOT NULL COMMENT 'Teacher User ID',
							 CHANGE `branch` `branch` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Branch Name',
							 CHANGE `subject_code` `subject_code` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Subject Code';

-- For `user_details` table
ALTER TABLE `user_details` CHANGE `userId` `userId` INT(11) NOT NULL COMMENT 'User ID',
						 CHANGE `address` `address` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Local Address',
						 CHANGE `pincode` `pincode` INT(11) NOT NULL COMMENT 'Local Pincode',
						 CHANGE `mobile_no` `mobile_no` VARCHAR(14) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'L MobileNumber',
						 CHANGE `address2` `address2` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Permanent Address',
						 CHANGE `pincode2` `pincode2` INT(11) NOT NULL COMMENT 'P Pincode',
						 CHANGE `mobile_no2` `mobile_no2` VARCHAR(14) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'P Mobile Number',
						 CHANGE `sex` `sex` CHAR(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'M' COMMENT 'Gender',
						 CHANGE `date` `date` VARCHAR(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '01-01-2001' COMMENT 'Date of Birth',
						 CHANGE `birth_place` `birth_place` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Birth Place',
						 CHANGE `religion` `religion` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Religion',
						 CHANGE `caste` `caste` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' COMMENT 'User Caste',
						 CHANGE `category` `category` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Category(Open/...)',
						 CHANGE `blood_group` `blood_group` VARCHAR(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' COMMENT 'Blood Group';

ALTER TABLE `verification_code` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique Code ID',
							 CHANGE `user_id` `user_id` INT(11) NOT NULL COMMENT 'UserID',
							 CHANGE `code` `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Verification Code',
							 CHANGE `creation_date` `creation_date` INT(11) NOT NULL COMMENT 'Creation Date',
							 CHANGE `expiration_date` `expiration_date` INT(11) NOT NULL COMMENT 'Expiry Date of Code',
							 CHANGE `type` `type` INT(11) NOT NULL DEFAULT '0' COMMENT '1 = Reset Password, 0= New Account Verification';
