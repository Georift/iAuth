CREATE TABLE IF NOT EXISTS `fail_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `counted` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;