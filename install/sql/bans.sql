CREATE TABLE IF NOT EXISTS `bans` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `expires` int(30) NOT NULL,
  `exception` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;