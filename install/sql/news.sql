CREATE TABLE IF NOT EXISTS `news` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `aid` int(30) NOT NULL,
  `time` int(30) NOT NULL,
  `content` longtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;