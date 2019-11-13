-- 02092016
-- Adds some Default Users
INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`) VALUES ('student', '$2a$06$ewK/cO7qHzyF/Szr3rlope0dbSjrHApvwraiyXzBARtB.6sJfOvoG', 'test@test.com', 'Student', 1, 1);
INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`) VALUES ('teacher', '$2a$06$ewK/cO7qHzyF/Szr3rlope0dbSjrHApvwraiyXzBARtB.6sJfOvoG', 'test@test.com', 'Teacher', 1, 2);
INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`) VALUES ('office', '$2a$06$ewK/cO7qHzyF/Szr3rlope0dbSjrHApvwraiyXzBARtB.6sJfOvoG', 'test@test.com', 'Office Staff', 1, 4);
INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`) VALUES ('admin', '$2a$06$ewK/cO7qHzyF/Szr3rlope0dbSjrHApvwraiyXzBARtB.6sJfOvoG', 'test@test.com', 'Admin', 1, 8);
INSERT INTO `login` (`user`, `password`, `email`, `name`, `verified`, `title`) VALUES ('superadmin', '$2a$06$ewK/cO7qHzyF/Szr3rlope0dbSjrHApvwraiyXzBARtB.6sJfOvoG', 'test@test.com', 'Super Admin', 1, 16);
