<?php
/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Bill Model
 *
 * @since  0.0.1
 */
class BillsModelBill extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Bills', $prefix = 'BillsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_bills.bill',
			'bill',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_bills.edit.bill.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  \JObject|boolean  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		// Get the details
		$data = parent::getItem($pk);

		// Convert the data according to the view
		$data->for_users = explode(',', $data->for_users);
		$data->attachments = json_decode($data->attachments);

		// If attachements are present get the attachment url
		if(count($data->attachments) > 0)
		{
			$params = JComponentHelper::getParams( 'com_bills' );
			$target_dir = $params->get ('file_store_path');
			if (is_array($data->attachments))
			{
				$data->filename = $data->attachments[0];
				$data->attachments[0] = JURI::root() . $target_dir . $data->id . '/' . $data->attachments[0];
			}
			else
			{
				$data->attachments = array();
			}

		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		// Get the user input & files
		$jinput = JFactory::getApplication()->input;
		$files = $jinput->files->get('jform');
		unset($data['attachments']);
		round($data['amount'], 2);

		// Set the attachments in data
		if (isset($files['attachments']['name']) && !empty($files['attachments']['name']))
		{
			$data['attachments'] = $files['attachments']['name'];
		}
		else
		{
			unset($data['attachments']);
		}

		if (isset($data['for_users']) && !empty($data['for_users']))
		{
			$membersString = implode(',', $data['for_users']);
			$data['for_users'] = $membersString;
		}

		// If bill already created then don't sent created_by field
		if($data['id'])
		{
			unset($data['created_by']);
		}

		if(parent::save($data))
		{
			$data['id'] = empty($data['id']) ? $this->getState($this->getName() . '.id') : $data['id'];

			if($data['id'])
			{
				$this->deleteEntryFromMapTable($data['id']);

				if(isset($data['attachments']))
				{
					$data['attachments'] = json_encode(array($this->storeFile($data['id'])));
					parent::save($data);
				}

				return $this->addEntryIntoMapTable($data);
			}
		}

		return false;
	}

	/**
	 * Method to delete the rows for existing billId
	 *
	 * @param   int  $billId  The bill id.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   0.0.1
	 */
	public function deleteEntryFromMapTable($billId)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// delete the existing ids from #__bill_user_map
		$conditions = array(
		    $db->quoteName('bill_id') . ' = ' . (int) $billId
		);

		$query->delete($db->quoteName('#__bill_user_map'));
		$query->where($conditions);

		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method to add new row in the database table.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   0.0.1
	 */
	public function addEntryIntoMapTable($data)
	{
		$queries = array();

		// Get a db connection.
		$db = JFactory::getDbo();

		// Get the data to save
		$users = explode(',', $data['for_users']);
		$billId = $data['id'];
		$amount = $data['amount'];
		$usersCount = count($users);

		// Check if amount and users are present and greater than 0
		if($amount > 0 &&  $usersCount> 0)
		{
			// Calculate the per user amount
			$perUserAmount = round($amount / $usersCount, 2);

			// For each user build the query
			foreach ($users as $user)
			{
				// Create a new query object.
				$query = $db->getQuery(true);

				// Insert columns.
				$columns = array('id', 'bill_id', 'user_id', 'amount');

				// Insert values.
				$values = array(0, $billId, $user, $perUserAmount);

				// Prepare the insert query.
				$query
				    ->insert($db->quoteName('#__bill_user_map'))
				    ->columns($db->quoteName($columns))
				    ->values(implode(',', $values));

				// Set the query
				$db->setQuery($query);

				if(!$db->execute())
				{
					return false;
				}
			}

			return true;
		}
	}

	/**
	 * Method to save the uploaded bill image.
	 *
	 * @param   int  $billId  The bill id.
	 *
	 * @return  string  File name.
	 *
	 * @since   0.0.1
	 */
	public function storeFile($billId)
	{
		// Expiration time in seconds
		$params = JComponentHelper::getParams( 'com_bills' );
		$target_dir = $params->get ('file_store_path');

		// Get the user input & files
		$jinput = JFactory::getApplication()->input;
		$files = $jinput->files->get('jform');

		// Set the profile image names in data
		if (isset($files['attachments']['name']))
		{
			$data['attachments'] = $files['attachments']['name'];
		}
		else
		{
			unset($data['attachments']);
		}

		if (!empty($files['attachments']['name']))
		{
			$attachmentImgUrl = JPATH_SITE . '/' . $target_dir . $billId . '/' . $files['attachments']['name'];

			// Move uploaded files to destination
			JFile::upload($files['attachments']['tmp_name'], $attachmentImgUrl);
		}

		return $files['attachments']['name'];
	}
}
