<?php
/**
 *	Holds all of the sql templates for execute.
 */

/**
 *	Where all of our table data will be held.
 */
$table;

/**
 *	Our table data:
 */

$table[] = "CREATE TABLE `access_log` (
  `id` int(30) NOT NULL auto_increment,
  `time` int(30) NOT NULL,
  `aid` int(30) NOT NULL,
  `ip` varchar(50) character set utf8 NOT NULL,
  `lid` int(30) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3";

$table[] = "CREATE TABLE `applications` (
  `id` int(30) NOT NULL auto_increment,
  `oid` int(30) NOT NULL,
  `name` varchar(255) collate latin1_general_ci NOT NULL,
  `unique_key` varchar(32) collate latin1_general_ci NOT NULL,
  `active` int(30) NOT NULL,
  `defaults` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;";

$table[] = "CREATE TABLE `app_sessions` (
  `id` int(30) NOT NULL auto_increment,
  `lid` int(30) NOT NULL,
  `hash` varchar(255) collate latin1_general_ci NOT NULL,
  `ip` varchar(255) collate latin1_general_ci NOT NULL,
  `expires` int(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=18 ;";

$table[] = "CREATE TABLE `bans` (
  `id` int(30) NOT NULL auto_increment,
  `ip` varchar(255) collate latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `expires` int(30) NOT NULL,
  `exception` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;";

$table[] = "CREATE TABLE `fail_log` (
  `id` int(30) NOT NULL auto_increment,
  `type` varchar(30) collate latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `ip` varchar(255) collate latin1_general_ci NOT NULL,
  `counted` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=13 ;";

$table[] = "CREATE TABLE `licences` (
  `id` int(30) NOT NULL auto_increment,
  `aid` int(30) NOT NULL,
  `hwid` varchar(255) collate latin1_general_ci NOT NULL,
  `expires` varchar(30) collate latin1_general_ci NOT NULL,
  `active` int(1) NOT NULL default '0',
  `serial` varchar(32) collate latin1_general_ci NOT NULL,
  `user` varchar(30) collate latin1_general_ci NOT NULL,
  `pass` varchar(32) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;";

$table[] = "CREATE TABLE `news` (
  `id` int(30) NOT NULL auto_increment,
  `aid` int(30) NOT NULL,
  `time` int(30) NOT NULL,
  `content` longtext collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";

$table[] = "CREATE TABLE `settingsgroup` (
  `id` int(30) NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci NOT NULL,
  `order` int(30) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;";

$table[] = "CREATE TABLE `settingsitems` (
  `id` int(30) NOT NULL,
  `sid` int(30) NOT NULL,
  `code` varchar(30) collate latin1_general_ci NOT NULL,
  `name` varchar(255) collate latin1_general_ci NOT NULL,
  `type` varchar(255) collate latin1_general_ci NOT NULL,
  `value` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

$table[] = "CREATE TABLE `users` (
  `id` int(30) NOT NULL auto_increment,
  `user` varchar(32) collate latin1_general_ci NOT NULL,
  `pass` varchar(32) collate latin1_general_ci NOT NULL,
  `email` varchar(255) collate latin1_general_ci NOT NULL,
  `activated` int(1) NOT NULL default '0',
  `lastlogin` int(30) NOT NULL,
  `lasthost` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user` (`user`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;";

?>