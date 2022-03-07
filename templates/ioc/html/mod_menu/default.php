<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
?>
<?php // The menu class is deprecated. Use nav instead. ?>
<?php

$topmenu = false;
$featured = (JRequest::getCmd('view') == 'featured'); // Front page

if ($params->get('menutype') == 'topmenu') {
	$class = "nav navbar-nav";
	$menunavclass = "";
	$topmenu = true;
} else {
	$class = "nav navbar-nav";
	$menunavclass = "";

}

?>
<ul class="<?php echo $class;?>"<?php
	$tag = '';

	if ($params->get('tag_id') != null)
	{
		$tag = $params->get('tag_id') . '';
		echo ' id="' . $tag . '"';
	}
?>>
<?php

$specialstyles = array (
	'Ioc-studies',
	'Ioc-employment',
);

$specialclass = '';

array_push($specialstyles, 'Ioc-sub_studies');
array_push($specialstyles, 'Ioc-sub_menu');

$specialstyle = in_array($params->get('style'), $specialstyles);

foreach ($list as $i => &$item)
{
	$item->tabindex = 0;
	if ($topmenu) {
		$item->tabindex = $i + 3;
	}
	$dataattr = '';

	if ($item->params->get('menu-meta_keywords')) {
		$dataattr = 'data-meta-keyword="' . $item->params->get('menu-meta_keywords') . '"';
	}

	$class = $menunavclass . $specialclass . 'item-' . $item->id;

	if (($item->id == $active_id) OR ($item->type == 'alias' AND $item->params->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type == 'alias')
	{
		$aliasToId = $item->params->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($item->type == 'separator')
	{
		$class .= ' divider';
	}

	if ($item->deeper)
	{
		$class .= ' deeper';
	}

	if ($item->parent)
	{
		$class .= ' parent';
	}

	if (!empty($class))
	{
		$class = ' class="' . trim($class) . '"';
	}

	echo '<li' . $class . ' ' . $dataattr . '>';

	if ($specialstyle) {
		$anchorcss = !empty($item->anchor_css) ? $item->anchor_css : '';
		$style = '';
		$stylemobile = '';
		$hexagon = '';
		$ariaattribute = $anchorcss == 'noclickable' ? 'aria-disabled="true" ' : ' ';
		if ($item->menu_image) {
			$path_parts = pathinfo($item->menu_image);
			if ($params->get('style') == 'Ioc-studies') {
				$hexagon = '<img src="' . join(DIRECTORY_SEPARATOR, array($path_parts['dirname'], $path_parts['filename'] . '-hexagon.svg')) . '" alt="" />';
			}
			$stylemobile = 'style="background-image: url(\''. join(DIRECTORY_SEPARATOR, array($path_parts['dirname'], $path_parts['filename'] . '-mobile.' . $path_parts['extension'])) . '\')"';
			$style = 'style="background-image: url(\''. $item->menu_image .'\')"';
		}
		echo '<a href="'. $item->flink .'" class="'. $anchorcss .'" '. $ariaattribute .'><div class="element-img-container"><div class="visible-xs element-img" '. $stylemobile . '></div><div class="hidden-xs element-img ' . $item->menu_image_css .'" '. $style . '>' . $hexagon . '</div></div>';
	}

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
		case 'heading':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;

	if ($specialstyle) {
		echo '</a>';
		if (!empty($item->params->get('menu-meta_description'))) {
			echo '<div class="meta-separator hidden-sm hidden-xs"></div><div class="meta-description hidden-sm hidden-xs">'. $item->params->get('menu-meta_description') .'</div>';
		}
	}
	// The next item is deeper.
	if ($item->deeper)
	{
		echo '<ul class="nav-child unstyled small">';
	}
	elseif ($item->shallower)
	{
		// The next item is shallower.
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	else
	{
		// The next item is on the same level.
		echo '</li>';
	}
}
?></ul>
