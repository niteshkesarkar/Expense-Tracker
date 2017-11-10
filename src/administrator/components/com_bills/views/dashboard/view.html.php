<?php

/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * View class for Dashboard.
 *
 * @since  1.6
 */
class BillsViewDashboard extends JViewLegacy
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
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		$this->count = new StdClass();
		$helper = new BillsHelper();

		// Get the count of users, drivers and stops
		$this->count->users   = $helper->getTotalCount('users');
		$this->count->types = $helper->getTotalCount('bill_bill_types');
		$this->count->groups   = $helper->getTotalCount('bill_groups');
		$this->total->expense   = $helper->getTotalExpense();

		$this->groupList1 = $helper->getGroupList('search-filter-group-list1');
		$this->groupList2 = $helper->getGroupList('search-filter-group-list2');

		// Set the tool-bar and number of found items
		$this->addToolBar();
		$helper->addSideBar('dashboard');

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
		JToolBarHelper::title('Dashboard');
		/*JToolBarHelper::addNew('group.add');
		JToolBarHelper::editList('group.edit');
		JToolBarHelper::deleteList(JText::_('Are you sure to detete this group?'), 'groups.delete', JText::_('Delete'));*/
	}

}
