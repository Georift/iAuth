CREATE TABLE IF NOT EXISTS `licences` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `aid` int(30) NOT NULL,
  `hwid` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `expires` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `serial` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `user` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `pass` varchar(32) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;