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

$table[] = "CREATE TABLE IF NOT EXISTS `access_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `time` int(30) NOT NULL,
  `aid` int(30) NOT NULL,
  `ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lid` int(30) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `oid` int(30) NOT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `version` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `unique_key` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `active` int(30) NOT NULL,
  `defaults` int(1) NOT NULL DEFAULT '0',
  `login` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5";

$table[] = "CREATE TABLE IF NOT EXISTS `app_sessions` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `lid` int(30) NOT NULL,
  `hash` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `expires` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=20 ";

$table[] = "CREATE TABLE IF NOT EXISTS `bans` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `expires` int(30) NOT NULL,
  `exception` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `fail_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `time` int(30) NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `counted` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=16 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `licences` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `aid` int(30) NOT NULL,
  `hwid` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `expires` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `serial` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `user` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `pass` varchar(32) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1607 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `news` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `aid` int(30) NOT NULL,
  `time` int(30) NOT NULL,
  `content` longtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `settingsgroup` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order` int(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;";

$table[] = "CREATE TABLE IF NOT EXISTS `settingsitems` (
  `id` int(30) NOT NULL,
  `sid` int(30) NOT NULL,
  `code` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `value` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

$table[] = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `user` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `pass` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `activated` int(1) NOT NULL DEFAULT '0',
  `lastlogin` int(30) NOT NULL,
  `lasthost` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;";

?>