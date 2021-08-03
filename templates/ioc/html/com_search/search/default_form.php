<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="post">
    <div class="btn-toolbar">
        <div class="btn-group">
            <input type="text" name="searchword" title="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
            <button name="Search" onclick="this.form.submit()" class="btn hasTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_SEARCH_SEARCH');?>">
                <span class="icon-search"></span>
                <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
            </button>
        </div>
        <input type="hidden" name="task" value="search" />
        <div class="clearfix"></div>
    </div>
    <?php if ($this->params->get('search_phrases', 1)) : ?>
        <fieldset class="phrases">
            <legend>
                <?php echo JText::_('TPL_IOC_FILTERS'); ?>
            </legend>
            <div class="phrases-box">
                <?php echo $this->lists['searchphrase']; ?>
            </div>
            <div class="ordering-box">
                <label for="ordering" class="ordering">
                    <?php echo JText::_('COM_SEARCH_ORDERING'); ?>
                </label>
                <?php echo $this->lists['ordering']; ?>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php if (!empty($this->searchword)) : ?>
        <div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
            <p>
                <?php echo JText::plural('TPL_IOC_SEARCH_KEYWORD_N_RESULTS', '<span class="total_results">' . $this->total . '</span>'); ?>
            </p>
            <?php if ($this->total > 0) : ?>
                <div class="form-limit">
                    <label for="limit">
                        <?php echo JText::_('TPL_IOC_DISPLAY_NUM'); ?>
                    </label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                    <span>
                        <?php echo JText::_('TPL_IOC_RESULTS_PAGE'); ?>
                    </span>
                </div>
                <p class="counter">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</form>
