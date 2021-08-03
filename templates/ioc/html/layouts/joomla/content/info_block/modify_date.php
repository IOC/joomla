<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

?>
            <dd class="modified">
                <time datetime="<?php echo JHtml::_('date', $displayData['item']->modified, 'c'); ?>" itemprop="dateModified">
                    <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $displayData['item']->modified, 'l, j F Y')); ?>
                </time>
            </dd>