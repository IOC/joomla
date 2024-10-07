<?php
/**
 * @copyright	Copyright (c) 2019-2022 by eclent. All rights reserved.
 * @license		GNU General Public License v2.0 or later.
 */

// no direct access
defined('_JEXEC') or die;


class PlgSystemForcepasswordresetInstallerScript {


	function install($parent) {

		$db = JFactory::getDbo();

    // Create a plugin table if not exists
    $query = $db->getQuery(true);
    $query = "CREATE TABLE IF NOT EXISTS `#__forcepasswordreset_pwdlogs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `pwd_hash` varchar(100) NOT NULL,
      `pwd_creation_date` datetime ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;";
    $db->setQuery($query);
    $db->execute();

	}


	function update($parent) {

		$db = JFactory::getDbo();

    // Create a plugin table if not exists
    $query = $db->getQuery(true);
    $query = "CREATE TABLE IF NOT EXISTS `#__forcepasswordreset_pwdlogs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `pwd_hash` varchar(100) NOT NULL,
      `pwd_creation_date` datetime ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;";
    $db->setQuery($query);
    $db->execute();

	}


	function uninstall($parent) {

    $db = JFactory::getDbo();

		// Delete the plugin table if exists
		$query = $db->getQuery(true);
		$query = "DROP TABLE IF EXISTS `#__forcepasswordreset_pwdlogs`;";
		$db->setQuery($query);
		$db->execute();

  }


}

?>
