<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$data     = $this->data;
$title    = JText::sprintf('COM_CONTACTIOC_EMAIL_SENT_TITLE', $data['typename']);
$text     = JText::sprintf('COM_CONTACTIOC_EMAIL_SENT', $data['study'], $data['email']);
$home     = JText::_('HOME');
$homepage = JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE');

$html = <<<HTML
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4>
        $title
    </h4>
    <p>
        $text
    </p>
</div>
<a class="contact-gohome" href="$this->baseurl" title="$home">$homepage</a>
HTML;

if ($this->script) {
    echo json_encode(array(
            'html' => $html
        )
    );
} else {
    echo $html;
}
