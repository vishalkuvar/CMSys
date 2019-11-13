-- 20160906
-- Change the Year to Semester

ALTER TABLE `login` CHANGE `year` `semester` VARCHAR(24) NOT NULL DEFAULT 'Sem0';