<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="col-lg-12 col-md-12 col-sm-12 footer2<?php echo $moduleclass_sfx ?>">
    <div class="footer-separator visible-xs visible-sm"></div>
    <div class="container">
        <div class="logo-gen col-xs-12 col-sm-12 col-md-2 col-lg-2 text-left">
            <a accesskey="5" target="_self" title="http://www.gencat.cat" href="http://www.gencat.cat" class="col-xs-12 col-sm-12">
                <img src="<?php echo JURI::base().'templates/'.$template.'/images/logo-generalitat.svg';?>" width="101" height="27" alt="http://www.gencat.cat" class="adaptImage">
            </a>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-10">
            <p>
                <a href="avis-legal"><?php echo JText::_('TPL_IOC_LEGAL_NOTICE'); ?></a>: <?php echo JText::_('TPL_IOC_LEGAL_MESSAGE'); ?>
            </p>
        </div>
    </div>
</div>
