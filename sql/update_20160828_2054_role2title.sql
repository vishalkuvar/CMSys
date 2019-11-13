-- 28082016
-- Change role to title in login table
ALTER TABLE `login` CHANGE `role` `title` INT(11) NOT NULL DEFAULT '0';
