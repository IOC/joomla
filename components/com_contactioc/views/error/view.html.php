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
 * Class for email sent view.
 *
 * @since  1.5
 */
class ContactiocViewError extends JViewLegacy
{
    public $error;
    public $script;

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
        parent::display();
        exit;
    }
}
