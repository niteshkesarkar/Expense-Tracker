<?php

/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Bill controller class.
 *
 * @since  0.0.1
 */
class BillsControllerBill extends JControllerForm
{
	public function getUsersFromGroup()
	{
		$app = JFactory::getApplication();
		$groupId = $app->input->get('groupId');

		$helper = new BillsHelper;

		$data = $helper->getUsersFromGroup($groupId);

		echo json_encode($data);
		jexit();
	}
}
