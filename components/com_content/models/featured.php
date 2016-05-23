<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('ContentModelArticles', __DIR__ . '/articles.php');

/**
 * Frontpage Component Model
 *
 * @since  1.5
 */
class ContentModelFeatured extends ContentModelArticles
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_content.frontpage';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   The field to order on.
	 * @param   string  $direction  The direction to order on.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();
		$app   = JFactory::getApplication('site');

		// List state information
		$limitstart = $input->getUInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		$params = $this->state->params;
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);

		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
		$this->setState('list.limit', $limit);
		$this->setState('list.links', $params->get('num_links'));

		$this->setState('filter.frontpage', true);

		if ((!$user->authorise('core.edit.state', 'com_content')) &&  (!$user->authorise('core.edit', 'com_content')))
		{
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}

		// Process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}

		// Check for category selection
		if ($params->get('featured_categories') && implode(',', $params->get('featured_categories')) == true)
		{
			$featuredCategories = $params->get('featured_categories');
			$this->setState('filter.frontpage.categories', $featuredCategories);
		}

		$articleOrderby   = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby  = $params->def('orderby_pri', '');

		$secondary = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);
		$primary   = ContentHelperQuery::orderbyPrimary($categoryOrderby);

		$this->setState('list.ordering', $primary . $secondary . ', a.created DESC');
		$this->setState('list.direction', '');
	}

	/**
	 * Method to get a list of articles.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		$params = clone $this->getState('params');
		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');

		if ($limit > 0)
		{
			$this->setState('list.limit', $limit);

			return parent::getItems();
		}

		return array();
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= $this->getState('filter.frontpage');

		return parent::getStoreId($id);
	}

	/**
	 * Get the list of items.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$query = parent::getListQuery();

		// Filter by categories
		$featuredCategories = $this->getState('filter.frontpage.categories');

		if (is_array($featuredCategories) && !in_array('', $featuredCategories))
		{
			$query->where('a.catid IN (' . implode(',', $featuredCategories) . ')');
		}

		return $query;
	}

	public function getImportant()
	{
		$params = clone $this->getState('params');
		$id_category = $params->get('important_category');

	    $query = 'SELECT title'.
	                ' FROM #__categories'.
	                ' WHERE id ='.(int) $id_category;
	    $Arows = $this->_getList($query);

	    //Agafem l'unic element
	    $categoria = array_pop($Arows);

	    $query = 'SELECT id, alias, title, introtext, created, modified, catid, language, cf.ordering'.
	                ' FROM #__content c'.
	                ' JOIN #__content_frontpage cf ON cf.content_id=c.id'.
	                ' WHERE state = 1'.
	                ' AND featured = 1'.
	                ' AND catid ='. (int) $id_category .
	                ' ORDER BY cf.ordering';
	    $articles = $this->_getList($query);

	    return $articles;
	}
}
