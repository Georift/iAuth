CREATE TABLE applications (id int(30) NOT NULL auto_increment,oid int(30) NOT NULL,`name` varchar(255) collate latin1_general_ci NOT NULL,unique_key varchar(32) collate latin1_general_ci NOT NULL,active int(30) NOT NULL,defaults int(1) NOT NULL default '0',PRIMARY KEY  (id)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;