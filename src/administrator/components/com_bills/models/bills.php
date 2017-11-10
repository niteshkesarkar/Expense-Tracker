<?php
/**
 * @package    com_bill
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Subscriptions model.
 *
 * @since  1.6
 */
class BillsModelBills extends JModelList
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
				'title',
				'created_by',
				'created_date',
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

		// Create the base select statement.
		$query	->select('*')
				->from($db->quoteName('#__bill_user_bills'));

		// Filter by duration.
		$search = $this->getState('filter.search');
		$createdDate = $this->getState('filter.created_date');
		$createdBy = $this->getState('filter.created_by');
		$paidFor = (string) $this->getState('filter.paid_for');
		$minimumAmount = $this->getState('filter.minimum_amount');
		$maximumAmount = $this->getState('filter.maximum_amount');
		$startDate = $this->getState('filter.start_date');
		$endDate = $this->getState('filter.end_date');

		if($search)
		{
			$query->where($db->quoteName('title') . ' LIKE ' . $db->quote('%' . $search . '%'));
		}

		if($paidFor)
		{
			$query->where($db->quoteName('for_users') . ' LIKE ' . $db->quote('%' . $paidFor . '%'));
		}

		if($createdDate)
		{
			$query->where($db->quoteName('created_date') . ' >= ' . $db->quote($createdDate));
		}

		if($createdBy)
		{
			$query->where($db->quoteName('created_by') . ' = ' . $db->quote($createdBy));
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

		if($startDate && $endDate)
		{
			$query->where($db->quoteName('created_date') . ' >= ' . $db->quote($startDate));
			$query->where($db->quoteName('created_date') . ' <= ' . $db->quote($endDate));
		}
		elseif($startDate)
		{
			$query->where($db->quoteName('created_date') . ' = ' . $db->quote($startDate));
		}
		elseif($endDate)
		{
			$query->where($db->quoteName('created_date') . ' <= ' . $db->quote($endDate));
		}

		$query->order($db->escape($this->getState('list.ordering', 'created_date')).' '.
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
	protected function populateState($ordering = 'id', $direction = 'desc')
	{
		parent::populateState($ordering, $direction);

		$app = JFactory::getApplication();

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
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
			$amountPerUser = $helper->getItemDetails('Type', $item->bill_type, 'allowed_expense');
			$count = count(explode(',', $item->for_users));
			$grantAmount = ($amountPerUser * $count);

			$item->reimburseAmount =  ($item->amount >= $grantAmount) ? $grantAmount : $item->amount;
			$item->reimburseAmount = round($item->reimburseAmount, 2);
			$item->amountDifference = round($item->amount - $grantAmount, 2);

			$itemsArray[] = $item;
		}

		return $itemsArray;
	}
}
