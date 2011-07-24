CREATE TABLE news (
  id int(30) NOT NULL auto_increment,
  aid int(30) NOT NULL,
  `time` int(30) NOT NULL,
  content longtext collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;