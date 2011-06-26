CREATE TABLE users (
  id int(30) NOT NULL auto_increment,
  `user` varchar(32) collate latin1_general_ci NOT NULL,
  pass varchar(32) collate latin1_general_ci NOT NULL,
  email varchar(255) collate latin1_general_ci NOT NULL,
  activated int(1) NOT NULL default '0',
  lastlogin int(30) NOT NULL,
  lasthost varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY `user` (`user`,email)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;