<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';
$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';
$ownstyles = array (
    'Ioc-studies',
    'Ioc-sub_studies',
    'Ioc-employment',
    'Ioc-sub_menu',
);

if (in_array($params->get('style'), $ownstyles)) {
    echo '<div class="element-title"><p>' . $item->title . '</p></div>';
    return;
}

$customclasses = explode(' ', $item->anchor_css);

if (in_array('iocmatricula', $customclasses)) {
    //$study_start = '<div class="newmatricula">' . JText::_('TPL_IOC_REGISTRATION_OPEN'). '</div>';
}

if ($item->menu_image)
{
    //$item->params->get('menu_text', 1) ?
    //$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
    //$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
    $linktype = $item->title;
}
else
{
    $linktype = $item->title;
}

switch ($item->browserNav)
{
    default:
    case 0:
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" tabindex="<?php echo $item->tabindex;?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
        break;
    case 1:
        // _blank
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" tabindex="<?php echo $item->tabindex;?>" target="_blank" <?php echo $title; ?>><?php echo $linktype;?></a><?php
        break;
    case 2:
    // Use JavaScript "window.open"
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" tabindex="<?php echo $item->tabindex;?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a>
<?php
        break;
}
