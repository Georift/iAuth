CREATE TABLE IF NOT EXISTS `access_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `time` int(30) NOT NULL,
  `aid` int(30) NOT NULL,
  `ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lid` int(30) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
