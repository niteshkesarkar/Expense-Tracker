<?php

/**
 * @package    com_bill
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * View class for a list of BillUserMaps.
 *
 * @since  0.0.1
 */
class BillsViewBillUserMaps extends JViewLegacy
{
/**
	* Display the view
	*
	* @param   string  $tpl  Template name
	*
	* @return void
	*
	* @throws Exception
	*/
	public function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state         = $this->get('State');
		$this->helper        = new BillsHelper;

		$listOrder     = $this->state->get('list.ordering');
		$listDirn      = $this->state->get('list.direction');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the tool-bar and number of found items
		$this->addToolBar();
		$this->helper->addSideBar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    0.0.1
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title('User Bill Map');
		/*JToolBarHelper::addNew('group.add');
		JToolBarHelper::editList('group.edit');
		JToolBarHelper::deleteList(JText::_('Are you sure to detete this group?'), 'groups.delete', JText::_('Delete'));*/
	}
}
