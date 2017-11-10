<?php
/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

/**
 * Dashboard model.
 *
 * @since  0.0.1
 */
class BillsModelDashboard extends JModelList
{
	public function getChartData($groupId, $id)
	{
		if($id == 'search-filter-group-list1')
		{
			$chartData = $this->getUsersFromGroup($groupId);
			$chartObj = $this->getChartObject($chartData);
		}
		elseif($id == 'search-filter-group-list2')
		{
			$chartData = $this->getTypesChartData($groupId);
			$chartObj = $this->getTypesChartObject($chartData);
		}
		else
		{
			$chartData = $this->getGroupChartData();
			$chartObj = $this->getTypesChartObject($chartData);
		}

		return $chartObj;
	}

	public function getGroupChartData()
	{
		$data = array();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('g.title, SUM(b.amount) as amount');
		$query->from($db->quoteName('#__bill_user_bills', 'b'));
		$query->join('INNER', $db->quoteName('#__bill_groups', 'g') . ' ON (' . $db->quoteName('b.bill_type') . ' = ' . $db->quoteName('g.id') .')');
		$query->order('g.title');
		$query->group('g.id');

		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Foreach user get his expenses
		foreach ($result as $type)
		{
			// Store the userId and amount
			$obj = new stdClass;
			$obj->title = $type->title;
			$obj->amount = $type->amount;

			$data[] = $obj;
		}

		return $data;
	}

	public function getTypesChartObject($chartData)
	{
		$data = array();

		foreach ($chartData as $obj)
		{
			$data['labels'][] = $obj->title;
			$data['data'][] = $obj->amount;
			$color = '';
			$color .= dechex(rand(10,254));
			$color .= dechex(rand(10,254));
			$color .= dechex(rand(10,254));
			$data['colors'][] = '#' . $color;
		}

		return $data;
	}

	public function getChartObject($chartData)
	{
		$data = array();

		foreach ($chartData as $obj)
		{
			$data['labels'][] = JFactory::getUser($obj->userId)->name;
			$data['data'][] = $obj->amount;
			$color = '';
			$color .= dechex(rand(10,254));
			$color .= dechex(rand(10,254));
			$color .= dechex(rand(10,254));
			$data['colors'][] = '#' . $color;
		}

		return $data;
	}

	public function getUsersFromGroup($groupId)
	{
		$data = array();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('members');
		$query->from($db->quoteName('#__bill_groups'));
		$query->where($db->quoteName('id') . ' = ' . $groupId);
		$db->setQuery($query);

		$result = $db->loadAssoc();

		$membersString = $result['members'];
		$membersArray = explode(',', $membersString);

		// Foreach user get his expenses
		foreach ($membersArray as $member)
		{
			$amount = $this->getUsersExpense($member);

			// Store the userId and amount
			$obj = new stdClass;
			$obj->userId = $member;
			$obj->amount = $amount;

			$data[] = $obj;
		}

		return $data;
	}

	public function getUsersExpense($userId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('SUM(amount) as amount');
		$query->from($db->quoteName('#__bill_user_map'));
		$query->where($db->quoteName('user_id') . ' = ' . $userId);
		$db->setQuery($query);

		$result = $db->loadAssoc();

		$amount = round($result['amount'], 2);

		return $amount;
	}

	public function getTypesChartData($groupId)
	{
		$data = array();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('t.title, SUM(b.amount) as amount');
		$query->from($db->quoteName('#__bill_user_bills', 'b'));
		$query->where($db->quoteName('group_id') . ' = ' . $groupId);
		$query->join('INNER', $db->quoteName('#__bill_bill_types', 't') . ' ON (' . $db->quoteName('b.bill_type') . ' = ' . $db->quoteName('t.id') .')');
		$query->order('t.title');
		$query->group('t.id');

		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Foreach user get his expenses
		foreach ($result as $type)
		{
			// Store the userId and amount
			$obj = new stdClass;
			$obj->title = $type->title;
			$obj->amount = $type->amount;

			$data[] = $obj;
		}

		return $data;
	}
}
