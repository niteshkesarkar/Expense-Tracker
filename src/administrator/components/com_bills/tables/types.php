<?php

/**
 * @package    com_bill
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * table class for Types
 *
 * @since  0.0.1
 */
class BillsTableTypes extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__bill_bill_types', 'id', $db);
	}
}
