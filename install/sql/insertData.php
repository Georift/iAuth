<?php
/**
 *	Holds all of the rows of information we need to include.
 */
 
/**
 *	Holds all of our rows of information.
 */
$rows;

/**
 *	Row data held below.
 */
 
$rows[] = "INSERT INTO `settingsgroup` (`id`, `name`, `order`) VALUES(1, 'Ban Settings', 1);";
$rows[] = "INSERT INTO `settingsitems` (`id`, `sid`, `code`, `name`, `type`, `value`) VALUES(0, 2, 'LOGTIME', 'How long will a login be?', 'text', '60')";
$rows[] = "INSERT INTO `settingsitems` (`id`, `sid`, `code`, `name`, `type`, `value`) VALUES(2, 1, 'BANENABLED', 'Bans Enabled', 'checkbox', '');";
$rows[] = "INSERT INTO `settingsitems` (`id`, `sid`, `code`, `name`, `type`, `value`) VALUES(3, 1, 'BANTIME', 'How long does a ban last?', 'text', '60');";
?>