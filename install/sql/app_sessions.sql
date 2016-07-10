CREATE TABLE IF NOT EXISTS `app_sessions` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `lid` int(30) NOT NULL,
  `hash` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `expires` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;