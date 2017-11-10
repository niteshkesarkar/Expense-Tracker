<?php
/**
 * @package    com_bills
 * @copyright  Copyright (C) 2017 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen','select');
JHtml::_('behavior.formvalidator');
?>

<form action="<?php echo JRoute::_('index.php?option=com_bills&layout=default&id='. (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12">
				  <ul class="nav nav-tabs">
					<li  class="active">
						<a href="#basic" aria-controls="basic" data-toggle="tab">
							<?php echo JText::_('Basic Details') ?>
						</a>
					</li>
				  </ul>

				<div class="tab-content">
					<div class="tab-pane active" id="basic">
						<fieldset>
						<?php foreach ($this->form->getFieldset('basic') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
							<div class="controls"><?php echo $field->input ; ?></div>
						</div>
					<?php endforeach; ?>
					</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="jform[state]" id="jform_state" value="1"/>
		<input type="hidden" name="jform[created_by]" id="jform_created_by" value="<?php echo JFactory::getUser()->id; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
