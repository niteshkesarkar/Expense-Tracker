<?php

/**
 * @package    com_bill
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Group View
 *
 * @since  0.0.1
 */
class BillsViewBill extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		$this->addToolBar();

		// Initialise the flag to show or hide the preview
		$this->showPreview = false;

		if (count($this->item->attachments) > 0 && !empty ($this->item->attachments[0]))
		{
			$this->showPreview = true;
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   0.0.1
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('Bill'));
		JToolBarHelper::apply('bill.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('bill.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('bill.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('bill.cancel', 'JTOOLBAR_CANCEL');
	}
}
