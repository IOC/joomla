<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ContactiocHelper', JPATH_COMPONENT . '/helpers/contactioc.php');

$controller = JControllerLegacy::getInstance('Contactioc');
$controller->registerDefaultTask('contactioc');
$controller->execute(JFactory::getApplication()->input->get('task'));
