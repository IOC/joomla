<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class for Mail.
 *
 * @since  1.5
 */
class ContactiocViewContactioc extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.5
	 */
	public function display($tpl = null)
	{
		$data = $this->getData();
		if ($data === false)
		{
			return false;
		}
		$this->set('data', $data);

		return parent::display($tpl);
	}

	/**
	 * Get the form data
	 *
	 * @return  object
	 *
	 * @since  1.5
	 */
	protected function &getData()
	{
		$app   = JFactory::getApplication();
		$data  = new stdClass;
		$input = $app->input;

		// Load with previous data, if it exists
		$data->contactType       = $input->get('contactType', 0, 'int');
		$data->contactStudy      = $input->get('contactStudy', 0, 'int');
		$data->contactSubject    = trim($input->get('contactSubject', '', 'string'));
		$data->contactContent    = trim($input->get('contactContent', '', 'string'));
		$data->contactFirstname  = trim($input->get('contactFirstname', '', 'string'));
		$data->contactLastname   = trim($input->get('contactLastname', '', 'string'));
		$data->contactEmail      = trim($input->get('contactEmail', '', 'string'));
		$data->contactTypeID     = $input->get('contactTypeID', 0, 'int');
		$data->contactID         = trim($input->get('contactID', '', 'string'));

		$data->contactEmail = JStringPunycode::emailToPunycode($data->contactEmail);

		return $data;
	}
}
