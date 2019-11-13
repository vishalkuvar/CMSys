-- 09092016
-- Increases log Size

ALTER TABLE `log` CHANGE `log` `log` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
