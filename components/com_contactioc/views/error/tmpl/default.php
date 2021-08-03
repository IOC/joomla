<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$error = $this->error;
$title = JText::_('COM_CONTACTIOC_ERROR');

$html = <<<HTML
<div id="contactError" class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4>$title</h4>
    <p>$error</p>
</div>
HTML;

if ($this->script)
{
    echo json_encode(array(
            'error' => true,
            'html' => $html
        )
    );
} else {
    echo $html;
}
