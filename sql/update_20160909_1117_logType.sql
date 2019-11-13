-- 09092016
-- Add's Log Type field into log table.
-- Helpful for Sorting

ALTER TABLE `log` ADD `log_type` INT NOT NULL AFTER `user_id`;