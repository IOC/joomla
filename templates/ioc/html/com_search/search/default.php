<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

?>
<div class="fluid-container page-header default">
    <div class="container">
        <h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
    </div>
</div>
<div class="item-page-default search<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1 class="page-title">
            <?php if ($this->escape($this->params->get('page_heading'))) : ?>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            <?php else : ?>
                <?php echo JText::_('TPL_IOC_SEARCH'); ?>
            <?php endif; ?>
        </h1>
    <?php endif; ?>
    <?php echo $this->loadTemplate('form'); ?>
    <?php if ($this->error == null && count($this->results) > 0) : ?>
        <?php echo $this->loadTemplate('results'); ?>
    <?php else : ?>
        <?php echo $this->loadTemplate('error'); ?>
    <?php endif; ?>
</div>