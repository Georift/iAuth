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

$rows[] = "INSERT INTO `settingsgroup` (`id`, `name`, `order`) VALUES
(1, 'Ban Settings', 3),
(3, 'General Settings', 1),
(2, 'Login Settings', 2);";

$rows[] = "INSERT INTO `settingsitems` (`id`, `sid`, `code`, `name`, `type`, `value`) VALUES
(0, 2, 'LOGTIME', 'How long will a login be?', 'text', '60'),
(2, 1, 'BANENABLED', 'Bans Enabled', 'checkbox', 'on'),
(3, 1, 'BANTIME', 'How long does a ban last?', 'text', '60'),
(6, 3, 'jqplot', 'Enable jqplot graphs <i>(experimental)</i>', 'checkbox', ''),
(7, 3, 'STATS', 'Allow statistics to be sent to the developers?', 'checkbox', 'on');";

?>