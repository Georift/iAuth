CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `oid` int(30) NOT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `version` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `unique_key` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `active` int(30) NOT NULL,
  `defaults` int(1) NOT NULL DEFAULT '0',
  `login` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;