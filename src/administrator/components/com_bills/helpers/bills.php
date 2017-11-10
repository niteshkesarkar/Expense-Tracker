<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2016 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;
/**
 * helper class for tjnotificationss
 *
 * @package     TJnotification
 * @subpackage  com_tjnotifications
 * @since       2.2
 */
class BillsHelper
{
	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    0.0.1
	 */
	function addSideBar($vName='')
	{
		JHtmlSidebar::addEntry(JText::_('<i class="icon-list"></i>  Dashboard'), 'index.php?option=com_bills&view=dashboard',$vName == 'dashboard');
		JHtmlSidebar::addEntry(JText::_('<i class="icon-tags"></i>  Types'), 'index.php?option=com_bills&view=types',$vName == 'types');
		JHtmlSidebar::addEntry(JText::_('<span class="icon-user"></span>  Groups'), 'index.php?option=com_bills&view=groups',$vName == 'groups');
		JHtmlSidebar::addEntry(JText::_('<i class="icon-pencil"></i>  Bills'), 'index.php?option=com_bills&view=bills',$vName == 'bills');
		JHtmlSidebar::addEntry(JText::_('<i class="icon-book"></i>  Bill User Maps'), 'index.php?option=com_bills&view=billusermaps',$vName == 'billusermaps');
	}

	function getItemDetails($modelName, $id, $field)
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bills/models');
		$model = JModelLegacy::getInstance($modelName, 'BillsModel');

		$itemDetails = $model->getItem($id);
		if (!empty($itemDetails))
		{
			return $itemDetails->$field;
		}

		return $id;
	}

	public function getTotalCount($tableName)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('count(id) as count')
			  ->from($db->quoteName('#__' . $tableName));

		$db->setQuery($query);
		$count = $db->loadObject()->count;

		return $count;
	}

	public function getTotalExpense()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('SUM(amount) as amount')
			  ->from($db->quoteName('#__bill_user_bills'));

		$db->setQuery($query);
		$amount = (integer) $db->loadObject()->amount;

		return $amount;
	}

	public function getGroupList($id = 'search-filter-group-list')
	{
		$data = array();

		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id as value, title as text')
				  ->from($db->quoteName('#__bill_groups'));

			$db->setQuery($query);
			$rows = $db->loadObjectlist();

			foreach($rows as $row)
			{
				$data[] = $row;
			}

			$obj = new stdClass;
			$obj->text = '';
			$obj->text = '-- Select Group --';

			array_unshift($data, $obj);
		}
		catch(Exception $e)
		{
		}

		$app = JFactory::getApplication();
		$stateVar = $app->getUserState('filter.groupId');
		$selectedField = isset($stateVar) ? $stateVar : '1';

		$options = array(
			'id' => $id, // HTML id for select field
			'name' => $id,
			'list.attr' => array(
				// Additional HTML attributes for select field
				'class' => 'filter-input input-medium',
				'onchange' => 'billsJs.dashboard.getGroupListChartData(this);'),
				// True to translate
				'list.translate' => false,
				// Key name for value in data array
				'option.key' => 'value',
				// Key name for text in data array
				'option.text' => 'text',
				'list.select' => $selectedField, // Value of the SELECTED field
		);

		$result = JHtmlSelect::genericlist($data, $id, $options);

		return $result;
	}

	public function getUsersFromGroup($groupId)
	{
		$options = array();
		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('members');
			$query->from($db->quoteName('#__bill_groups'));
			$query->where($db->quoteName('id') . ' = ' . $groupId);

			$db->setQuery($query);
			$rows = $db->loadObject();

			$members = explode(',', $rows->members);

			foreach($members as $userId)
			{
				$obj = new stdClass;
				$obj->value = $userId;
				$obj->text = JFactory::getUser($userId)->name;

				$options[] = $obj;
			}

			return $options;
		}
		catch(Exception $e)
		{
			return $options;
		}
	}
}
