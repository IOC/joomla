<?php

// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(__FILE__));
require_once JPATH_BASE.'/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_PLATFORM . '/src/Factory.php';

$db = JFactory::getDbo();


//$result = $db->query("SELECT * FROM `INFORMATION_SCHEMA`.`TABLES`");

//print_R($result);



echo "OK";
