<?php

/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Dashboard controller class.
 *
 * @since  0.0.1
 */
class BillsControllerDashboard extends JControllerAdmin
{
	function getGroupListChartData()
	{
		$app = JFactory::getApplication();
		$groupId = $app->input->get('groupId');
		$id = $app->input->get('id');
		$data = $app->setUserState("filter.groupId1", $groupId);

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bills/models');
		$model = JModelLegacy::getInstance('Dashboard', 'BillsModel');
		$data = $model->getChartData($groupId, $id);

		echo json_encode($data);
		jexit();
	}
}
