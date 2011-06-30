CREATE TABLE app_sessions (
  id int(30) NOT NULL auto_increment,
  lid int(30) NOT NULL,
  `hash` varchar(255) collate latin1_general_ci NOT NULL,
  ip varchar(255) collate latin1_general_ci NOT NULL,
  expires int(30) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;