-- 20160906
-- Table to track which file can be accessed by whom.

--
-- Table structure for table `files`
--
CREATE TABLE IF NOT EXISTS `files` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`file_type` int(11) NOT NULL DEFAULT '-1',
	`file_name` varchar(255) NOT NULL,
	`branch` int(11) NOT NULL DEFAULT '0',
	`semester` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
