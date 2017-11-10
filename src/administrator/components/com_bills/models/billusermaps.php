<?php
/**
 * @package    com_bill
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * BillUserMaps model.
 *
 * @since  1.6
 */
class BillsModelBillUserMaps extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      0.0.1
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'bill_id',
				'user_id',
				'amount'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    0.0.1
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$paidFor = $this->getState('filter.paid_for');
		$minimumAmount = $this->getState('filter.minimum_amount');
		$maximumAmount = $this->getState('filter.maximum_amount');


		// Create the base select statement.
		$query	->select('*')
				->from($db->quoteName('#__bill_user_map'));

		if($paidFor)
		{
			$query->where($db->quoteName('user_id') . ' = ' . $db->quote($paidFor));
		}

		if($minimumAmount > 0 && $maximumAmount > 0)
		{
			$query->where($db->quoteName('amount') . ' >= ' . $db->quote($minimumAmount));
			$query->where($db->quoteName('amount') . ' <= ' . $db->quote($maximumAmount));
		}
		elseif($minimumAmount > 0)
		{
			$query->where($db->quoteName('amount') . ' >= ' . $db->quote($minimumAmount));
		}
		elseif($maximumAmount > 0)
		{
			$query->where($db->quoteName('amount') . ' <= ' . $db->quote($maximumAmount));
		}

		$query->order($db->escape($this->getState('list.ordering', 'id')).' '.
		$db->escape($this->getState('list.direction', 'DESC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'id', $direction = 'asc')
	{
		parent::populateState($ordering, $direction);

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('list.limit', $limit);
		$this->setState('list.start', $limitstart);
	}

	public function getItems()
	{
		$itemsArray = array();

		$items = parent::getItems();

		$helper = new BillsHelper;

		foreach ($items as $item)
		{
			$grantAmount = $helper->getItemDetails('Type', $item->bill_id, 'allowed_expense');

			$item->reimburseAmount =  ($item->amount >= $grantAmount) ? $grantAmount : $item->amount;
			$item->reimburseAmount = round($item->reimburseAmount, 2);
			$item->amountDifference = round($item->amount - $grantAmount, 2);

			$itemsArray[] = $item;
		}

		return $itemsArray;
	}
}
