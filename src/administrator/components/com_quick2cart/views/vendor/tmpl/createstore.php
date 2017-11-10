<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

$lang = JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$comquick2cartHelper = new comquick2cartHelper;
$storeHelper=new storeHelper;

JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');

jimport('joomla.form.formvalidator');
jimport('joomla.html.pane');

// 1.check user is logged or not
$user = JFactory::getUser();
$mainframe = JFactory::getApplication();
$storeHelper=new storeHelper;

//added by aniket
$entered_numerics = "'" . JText::_('QTC_ENTER_NUMERICS') . "'";

// 3.CHECK MAX CREATE STORE LIMIT
if (empty($this->allowToCreateStore))
{
	$userStoreCount=$storeHelper->getUserStoreCount();
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
		<div class="well">
			<div class="alert alert-error">
				<span>
					<?php echo JText::sprintf('QTC_ALREADY_YOU_HAVE_STORES',$userStoreCount); ?>
				</span>
			</div>
		</div>
	</div>
	<?php
	return false;
}

$store_edit=0;
$store_vanity='0';

if (!empty($this->storeinfo[0]))
{
	$store_edit=0;
	$store_vanity=$this->storeinfo[0]->vanityurl;
}

$qtc_params = JComponentHelper::getparams('com_quick2cart');
$qtcshiphelper = new qtcshiphelper;
?>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function() {
		generateStoreState(<?php echo isset($this->storeinfo[0]->country)?$this->storeinfo[0]->country:0;?>, <?php echo !empty($this->storeinfo[0]->region)?$this->storeinfo[0]->region:"0";?>);
	});

	window.addEvent('domready', function()
	{
		document.formvalidator.setHandler('qtc_alphanum', function(value)
		{
			var regex = /^[0-9a-zA-Z\-]+$/;
			var status=regex.test(value);

			if (!status)
			{
				alert("<?php echo JText::_('QTC_ALPHA_NUM_ONLY'); ?>");
				techjoomla.jQuery('#store_alias').hide();
				techjoomla.jQuery('#storeVanityUrl').focus();
				/*added by aniket to make the textbox blank if worng value entered in vanity url*/
				techjoomla.jQuery('#storeVanityUrl').val('');
			}
			else
			{
				ckUniqueVanityURL();
			}

			return status;
		});
	});

	function myValidate(f)
	{
		if (document.formvalidator.isValid(f))
		{
			f.check.value="<?php echo JSession::getFormToken(); ?>";

			return true;
		}
		else
		{
			var msg = "<?php echo JText::_('COP_NOT_ACCEPTABLE_ENTERY');?>";
			alert(msg);
		}

		return false;
	}

	function paymode(mode)
	{
		if (mode==0)
		{
			techjoomla.jQuery('#paypalmodeDiv').show();
			techjoomla.jQuery('#othermodeDiv').hide();
			/* add and remove required class*/
			techjoomla.jQuery('#paypalemail').addClass('required');
			techjoomla.jQuery('#otherPayMethod').removeClass('required');
			techjoomla.jQuery('#otherPayMethod').removeAttr('required');
			techjoomla.jQuery('#otherPayMethod').val('');
		}
		else
		{
			techjoomla.jQuery('#paypalmodeDiv').hide();
			techjoomla.jQuery('#othermodeDiv').show();
			/*add and remove required class*/
			techjoomla.jQuery('#otherPayMethod').addClass('required');
			techjoomla.jQuery('#paypalemail').removeClass('required');
			techjoomla.jQuery('#paypalemail').removeAttr('required');
			techjoomla.jQuery('#paypalemail').val('');
		}
	}

	/*THIS FUNCTION CHECK WHETHER VANITY URL IS UNIQE OR NOT*/
	function ckUniqueVanityURL()
	{
		var editstore="<?php echo $store_edit;?>";
		var newvanityURL=document.qtcCreateStoreForm.storeVanityUrl.value;
		var oldVanity="<?php echo $store_vanity;?>";
		/*if not a edit task and not empty sku value then only call ajax*/
		/*if (editstore==0 && skuval)*/
		if (newvanityURL)
		{
			if (oldVanity != newvanityURL)
			{
				techjoomla.jQuery.ajax({
					url: '?option=com_quick2cart&controller=vendor&task=ckUniqueVanityURL&vanityURL='+newvanityURL+'&tmpl=component&format=raw',
					cache: false,
					type: 'GET',
					/*dataType: 'json',*/
					success: function(data)
					{
						/* already exist*/
						if (data == '1')
						{
							alert("<?php echo JText::_( 'QTC_VANITY_ALREADY_EXIST')?>");
							techjoomla.jQuery('#store_alias').hide();
							techjoomla.jQuery('#storeVanityUrl').focus();
						}
						elseif (! techjoomla.jQuery('#storeVanityUrl').hasClass('invalid'))
						{
							n=newvanityURL.replace(/([0-9]*)(:)/i,"$1-");
							techjoomla.jQuery('#store_alias span').html(n);
							techjoomla.jQuery('#store_alias').show();
						}
					}
				});
			}
		}
	}

	function qtcbuttonAction(actionName)
	{
		if (actionName=='vendor.cancel')
		{
			document.qtcCreateStoreForm.btnAction.value = actionName;
			document.qtcCreateStoreForm.task.value = actionName;
			document.qtcCreateStoreForm.submit();

			return true;
		}

		var valid = myValidate(document.qtcCreateStoreForm);

		if (valid == true)
		{
			document.qtcCreateStoreForm.btnAction.value = actionName;
			/*document.qtcCreateStoreForm.task.value = actionName;*/
			document.qtcCreateStoreForm.submit();
		}
	}

	function generateStoreState(field_name, valToSelect)
	{
		var countryId = 'storecountry';
		var country_value=techjoomla.jQuery('#'+countryId).val();

		if (valToSelect == 0)
		{
			var e = document.getElementById("qtcstorestate");
			var valToSelect = e.options[e.selectedIndex].value;
		}

		techjoomla.jQuery.ajax({
			type : "POST",
			url : "index.php?option=com_quick2cart&task=vendor.getRegions&tmpl=component&country_id="+country_value,
			success : function(response)
			{
				techjoomla.jQuery('#qtcstorestate').html(response);

				if (valToSelect > 0)
				{
					techjoomla.jQuery("#qtcstorestate option[value='" + valToSelect + "']").attr("selected", "true");
				}
			}
		});
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> store-form">
	<form name="qtcCreateStoreForm" id="qtcCreateStoreForm" class="form-validate form-horizontal" method="post" enctype="multipart/form-data" onSubmit="return myValidate(this);" >
		<?php
		$active = 'create_store';
		$comquick2cartHelper = new comquick2cartHelper;
		$storehelper = new storehelper();
		$user_stores=$storehelper->getuserStoreList();

		if (count($user_stores) >0)
		{
			if (!$mainframe->isAdmin())
			{
				$view=$comquick2cartHelper->getViewpath('vendor','toolbar');
				ob_start();
				include($view);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
			}
		}
		?>

		<legend>
			<?php echo (empty($this->storeinfo))? JText::_( "QTC_CREATE_VENDER") : JText::_( "QTC_EDIT_VENDER_STORE"); ?>
		</legend>

		<!--main div -->
		<div>
			<!--<div class="control-group">
				<label for="vendor_name" class="control-label"><?php // echo JHtml::tooltip(JText::_('VENDER_NAME_TOOLTIP'), JText::_('VENDER_NAME'), '', JText::_('VENDER_NAME'));?></label>
				<div class="controls">
					<input type="text" name="vendor_name" id="vendor_name" class="inputbox required validate-name"  size="20" value="<?php //if ($this->storeinfo){  echo stripslashes($this->storeinfo[0]->name); } ?>" />
				</div>
			</div>
			-->

			<div class="control-group">
				<label for="title" class="control-label"><?php echo JHtml::tooltip(JText::_('VENDER_TITLE_TOOLTIP'), JText::_('VENDER_TITLE'), '', '* '.JText::_('VENDER_TITLE'));?></label>
				<div class="controls">
					<input type="text" name="title" id="title" class="inputbox required" size="20" value="<?php if (!empty($this->storeinfo)){ echo $this->escape( stripslashes( $this->storeinfo[0]->title ) ); } ?>" />
					<!--<div class="text-warning">
						<p><?php //echo JText::_('COM_Q2C_ALPHANUM_NOTE'); ?></p>
					</div> -->
				</div>
			</div>

			<!-- for STORE VANITY URL like sitename.com/index.php/store/storevanity-->
			<!--Added by aniket .. show this only if SEF is on-->
			<?php
			$is_sef = $mainframe->getCfg('sef');

			if ($is_sef==1 && !$mainframe->isAdmin())
			{
				?>
				<div class="control-group">
					<label for="vendor_storeVanityUrl" class="control-label">
						<?php echo JHtml::tooltip(JText::_('VENDER_STORE_VANITY_URL_TOOLTIP'), JText::_('STORE_VANITY_URL'), '', JText::_('STORE_VANITY_URL'));?>
					</label>
					<div class="controls">
						<?php
						$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');

						/* @TODO JUGAD HERE for Vanity URL to display, DO NOT REMOVE &vanitydisplay=1 from $vanity_url */
						$vanity_url = JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=store&vanitydisplay=1&Itemid='.$Itemid), strlen(JUri::base(true)) + 1);
						$menu = JFactory::getApplication()->getMenu();
						$lang = JFactory::getLanguage();
						/* @TODO JUGAD HERE for adding index.php when category menu is default menu*/
						?>

						<input type="text" name="storeVanityUrl" id="storeVanityUrl"
							class="inputbox validate-qtc_alphanum " size="20"
							value="<?php if (!empty($this->storeinfo[0]->vanityurl)){ echo stripslashes($this->storeinfo[0]->vanityurl);}?>" placeholder="<?php echo JText::_("COM_QUICK2CART_VANITY_URL_HINT"); ?>" />

						<?php
						$multivendor_enable = $qtc_params->get('multivendor');

						if (!empty($multivendor_enable))
						{
							?>
							<span id="store_alias" style="<?php echo (empty($this->storeinfo[0]->vanityurl)) ? "display:none;": "";?>" class="help-inline">
								<strong><?php echo JText::_('QTC_VANITY_DES_EG').'&nbsp';?></strong>
								<i>
									<?php
										echo $vanity_url . (($Itemid==$menu->getDefault($lang->getTag())->id) ? 'index.php' : '') . '/';

										if (!empty($this->storeinfo[0]->vanityurl))
										{
											echo preg_replace('/([0-9]*)(:)/i', "$1-", stripslashes($this->storeinfo[0]->vanityurl));
										}

										echo '/' . JText::_('QTC_VANITY_PAGE');
									?>
								</i>
							</span>
							<?php
						}
						?>
					</div>
				</div>
			<?php
			}
			// if SEF if OFF ENDS....dont show vanity URL
			?>

			<hr class="hr hr-condensed"/>

			<?php if ($mainframe->isAdmin())
			{
				?>
				<!-- for user selection  -->
				<div class="control-group ">
					<label for="store_creator_name" class="control-label">
						<?php echo JHtml::tooltip(JText::_('COM_QUICK2CRT_STORE_OWNER_TITLE'), JText::_('COM_QUICK2CRT_STORE_OWNER'), '', JText::_('COM_QUICK2CRT_STORE_OWNER'));?>
					</label>
					<div class="controls">

						<?php
						/*if (version_compare(JVERSION, '3.5', 'ge'))
						{
							// Set the link for the user selection page
							$required = "required='required'";
							$userName = "";
							$class = "";
							$size = 0;
							$readonly = "";
							$id = "store_creator_id";
							$onchange = "";
							$value = isset($this->storeinfo[0]->owner) ? JFactory::getUser($this->storeinfo[0]->owner)->id : JFactory::getUser()->id;
							$userName = JFactory::getUser($value)->name;
							$name = "store_creator_id";
							$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;required='
								. ($required ? 1 : 0) . '&amp;field={field-user-id}';

							// Invalidate the input value if no user selected
							if (JText::_('JLIB_FORM_SELECT_USER') == htmlspecialchars($userName, ENT_COMPAT, 'UTF-8'))
							{
								$userName = "";
							}

							JHtml::script('jui/fielduser.min.js', false, true, false, false, true);
							?>
							<?php
							// Create a dummy text field with the user name. ?>
							<div class="field-user-wrapper"
								data-url="<?php echo $link; ?>"
								data-modal=".modal"
								data-modal-width="100%"
								data-modal-height="400px"
								data-input=".field-user-input"
								data-input-name=".field-user-input-name"
								data-button-select=".button-select"
							>
								<div class="input-append">
									<input
										type="text" id="<?php echo $id; ?>"
										value="<?php echo  htmlspecialchars($userName, ENT_COMPAT, 'UTF-8'); ?>"
										placeholder="<?php echo JText::_('JLIB_FORM_SELECT_USER'); ?>"
										readonly
										class="field-user-input-name <?php echo $class ? (string) $class : ''?>"
										<?php echo $size ? ' size="' . (int) $size . '"' : ''; ?>
										<?php echo $required ? 'required' : ''; ?>/>
										<?php
										if (!$readonly) : ?>
											<a class="btn btn-primary button-select" title="<?php echo JText::_('JLIB_FORM_CHANGE_USER') ?>">
												<span class="icon-user"></span>
											</a>
											<?php echo JHtml::_(
												'bootstrap.renderModal',
												'userModal_' . $id,
												array(
													'title'  => JText::_('JLIB_FORM_CHANGE_USER'),
													'closeButton' => true,
													'footer' => '<button class="btn" data-dismiss="modal">' . JText::_('JCANCEL') . '</button>'
												)
											); ?>
										<?php
										endif; ?>
								</div>
								<?php // Create the real field, hidden, that stored the user id. ?>
								<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo (int) $value; ?>"
													class="field-user-input <?php echo $class ? (string) $class : ''?>"
													data-onchange="<?php echo $this->escape($onchange); ?>"/>
							</div>
						<?php
						}*/
						if (JVERSION >= "3.5")
						{
							$userId = isset($this->storeinfo[0]->owner) ? JFactory::getUser($this->storeinfo[0]->owner)->id : JFactory::getUser()->id;

							$userFieldData = array ("required"=>1,
								"class" =>"",
								"size" => 0,
								"readonly" => "",
								"onchange" => "",
								"id" => "store_creator_id",
								"name" => "store_creator_id",
								"value" => $userId,
								"userName" => JFactory::getUser($userId)->name
								);

							$q2cLayout = new JLayoutFile('joomla.form.field.user');

							echo $q2cLayout->render($userFieldData);
						}
						else
						{ ?>

							<div class="input-append">
								<input type="text" id="store_creator_name" name="store_creator_name"
									class="input-medium required" disabled="disabled"
									placeholder="<?php echo JText::_('COM_QUICK2CRT_STORE_OWNER');?>"
									value="<?php echo (isset( $this->storeinfo[0]->owner)) ? JFactory::getUser($this->storeinfo[0]->owner)->name : JFactory::getUser()->name; ?>">
									<a class="modal qtc_modal  button btn btn-info modal_jform_created_by"
										rel="{handler: 'iframe', size: {x: 800, y: 500}}"
										href="index.php?option=com_users&view=users&layout=modal&tmpl=component&field=jform_created_by"
										title="<?php echo JText::_('COM_STORE_STORE_CREATOR');?>" >
											<i class="icon-user"></i>
									</a>
							</div>

							<input type="hidden" id="store_creator_id" name="store_creator_id"
								class="required"
								value="<?php echo (isset($this->storeinfo[0]->owner)) ? JFactory::getUser($this->storeinfo[0]->owner)->id : JFactory::getUser()->id; ?>" />
						<?php
						}
						?>
					</div>
				</div>
				<input type="hidden" name="storeVanityUrl" id="storeVanityUrl" value="<?php if (!empty($this->storeinfo[0]->vanityurl)){ echo stripslashes($this->storeinfo[0]->vanityurl);}?>"/>
				<?php
			}
			?>

			<div class="control-group">
				<label for="description" class="control-label">
					<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_VENDER_DESCRIPTION_TOOLTIP'), JText::_('VENDER_DESCRIPTION'), '', JText::_('VENDER_DESCRIPTION'));?>
				</label>
				<div class="controls">
					<!--
					<input type="text" name="description" id="description" class="inputbox required validate-name"   size="20" value="<?php //if ($this->storeinfo){ echo $this->escape( stripslashes( $this->storeinfo[0]->code ) ); } ?>" />
					-->
					<textarea  size="28" rows="3" name="description" id="description" class="inputbox" ><?php if (!empty($this->storeinfo)){ echo trim($this->storeinfo[0]->description);}?></textarea>
				</div>
			</div>

			<!-- Company name -->
			<div class="control-group">
				<label for="companyname" class="control-label"><?php echo JHtml::tooltip(JText::_('VENDER_COMPANY_NAME_TOOLTIP'), JText::_('COMPANY_NAME'), '',JText::_('COMPANY_NAME'));?></label>
				<div class="controls">
					<input type="text" name="companyname" id="companyname"
						class="inputbox" size="20"
						value="<?php if (!empty($this->storeinfo[0]->company_name)){ echo stripslashes($this->storeinfo[0]->company_name); } ?>" />
				</div>
			</div>

			<div class="control-group">
				<label for="email" class="control-label"><?php echo JHtml::tooltip(JText::_('VENDER_EMAIL_TOOLTIP'), JText::_('VENDER_EMAIL'), '', '* '.JText::_('VENDER_EMAIL'));?>
				</label>
				<div class="controls">
					<!--
					<input type="email" name="email" id="email" class="inputbox required validate-email"  size="20" value="<?php /*if (!$mainframe->isAdmin() && !empty($this->storeinfo)){  echo stripslashes($this->storeinfo[0]->store_email); } elseif (!$mainframe->isAdmin() && !empty($user->email)){echo $user->email;}*/?>" />
					-->
					<input type="email" name="email" id="email"
						class="inputbox required validate-email" size="20"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->store_email); } elseif (!empty($user->email)){echo $user->email;}?>" />
				</div>
			</div>
			<div class="control-group">
				<label for="phone" class="control-label"><?php echo JHtml::tooltip(JText::_('VENDER_PHONE_TOOLTIP'), JText::_('VENDER_PHONE'), '', '* '.JText::_('VENDER_PHONE'));?>
				</label>
				<div class="controls">
					<input type="text" name="phone" id="phone"
						class="inputbox required"
						onBlur="checkforalpha(this,'',<?php echo $entered_numerics; ?>);"
						size="20" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->phone);}?>" />
				</div>
			</div>

			<hr class="hr hr-condensed"/>

			<!-- ADDRESS -->
			<div class="control-group">
				<label for="address" class="control-label"><?php echo JHtml::tooltip(JText::_('VENDER_ADDRESS_TOOLTIP'), JText::_('VENDER_ADDRESS'), '','* '.JText::_('VENDER_ADDRESS'));?>
				</label>
				<div class="controls">
					<textarea  size="28" rows="3" name="address" id="address" class="inputbox required" ><?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->address);}?></textarea>
				</div>
			</div>

			<!--Land Mark-->
			<div class="control-group">
				<label for="land_mark" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY_TOOLTIP'), JText::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY'), '',JText::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY'));?>
				</label>
				<div class="controls">
					<input type="text" name="land_mark" id="land_mark" class=" inputbox" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->land_mark);}?>"></input>
				</div>
			</div>


			<div class="control-group">
				<label for="pincode" class="control-label "><?php echo JHtml::tooltip(JText::_('QTC_BILLIN_ZIP_DESC'), JText::_('QTC_BILLIN_ZIP'), '','* '.JText::_('QTC_BILLIN_ZIP'));?>
				</label>
				<div class="controls">
					<input type="text" name="pincode" id="pincode" class=" inputbox required" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->pincode);}?>"></input>
				</div>
			</div>

			<!--Country-->
			<div class="control-group">
				<label for="storecountry" class="control-label"><?php echo "* " . JText::_('QTC_BILLIN_COUNTRY')?></label>
				<div class="controls">
				<?php
					$country = $this->countrys;
					$options = array();
					$options[] = JHtml::_('select.option', "", JText::_('QTC_BILLIN_SELECT_COUNTRY'));

					foreach ($country as $key=>$value)
					{
						$options[] = JHtml::_('select.option', $value['id'], $value['country']);
					}

					if (!empty($this->storeinfo[0]->country))
					{
						$country = $this->storeinfo[0]->country;
					}
					else
					{
						$country = "";
					}

					echo $this->dropdown = JHtml::_('select.genericlist',$options,'storecountry','required="required" onchange=\'generateStoreState(id,"1")\' ','value','text', $country);
				?>

				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!--State-->
			<div class="control-group" >
				<label for="qtcstorestate" class="control-label"><?php echo "* " .  JText::_('QTC_BILLIN_STATE')?></label>
				<div class="controls">
					<select name="qtcstorestate" id="qtcstorestate" class="" >
						<option selected="selected"><?php echo JText::_('QTC_BILLIN_SELECT_STATE')?></option>
					</select>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!--City-->
			<div class="control-group">
				<label for="city" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_VENDER_CITY_TOOLTIP'), JText::_('COM_QUICK2CART_VENDER_CITY'), '','* '.JText::_('COM_QUICK2CART_VENDER_CITY'));?>
				</label>
				<div class="controls">
					<input type="text" name="city" id="city" class="inputbox required" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->city);}?>"></input>
				</div>
			</div>

			<hr class="hr hr-condensed"/>

			<!--avatar -->
			<div class="control-group">
				<label for="avatar" class="control-label">
					<?php echo JHtml::tooltip(JText::_('VENDER_AVTAR_TOOLTIP'), JText::_('VENDER_AVTAR'), '', JText::_('VENDER_AVTAR'));?>
				</label>
				<div class="controls">
					<?php
					$width  = $qtc_params->get('storeavatar_width');
					$height = $qtc_params->get('storeavatar_height');

					if (!empty($this->storeinfo[0]->store_avatar))
					{
						?>
						<input type="file" name="avatar" id="avatar"
							placeholder="<?php echo JText::_('COM_QUICK2CART_IMAGE_MSG');?>"
							accept="image/*" />
						<div class="text-warning">
							<p><?php echo JText::_('COM_Q2C_EXISTING_IMAGE_MSG');?></p>
						</div>
						<div class="text-info">
							<p><?php echo JText::_('COM_Q2C_EXISTING_IMAGE');?></p>
						</div>

						<div>
							<?php
							$img = '';

							if (!empty($this->storeinfo[0]->store_avatar))
							{
								$img = $comquick2cartHelper->isValidImg($this->storeinfo[0]->store_avatar);
							}

							if (empty($img))
							{
								$img = $storeHelper->getDefaultStoreImage();
							}
							?>

							<img class='img-rounded img-polaroid' src='<?php echo $img;?>' />
						</div>
						<?php
					}
					// While editing image field is not * required
					else
					{
						?>
						<input type="file" name="avatar" id="avatar"
							placeholder="<?php echo JText::_('COM_QUICK2CART_IMAGE_MSG');?>"
							class="" accept="image/*" />
						<?php
					}
					?>
					<div class="text-warning">
						<p><?php echo JText::sprintf('QTC_AVTAR_SIZE_MASSAGE', $height, $width);?></p>
						<p><?php echo JText::sprintf('COM_QUICK2CART_ALLOWED_IMG_FORMATS', 'gif, jpeg, jpg, png');?></p>
					</div>
				</div>
			</div>

			<!--  for STORE header  -->
			<!--
			<div class="control-group">
				<label for="vendor_storeheader" class="control-label"><?php // echo JHtml::tooltip(JText::_('VENDER_STORE_HEADER_TOOLTIP'), JText::_('STORE_HEADER'), '', JText::_('STORE_HEADER'));?></label>
				<div class="controls">
					<input type="file" name="storeheader"  placeholder="<?php //echo JText::_('COM_QUICK2CART_IMAGE_MSG');?>" accept="image/*">
					<span class="help-block"><?php //echo JText::_('QTC_HEADER_SIZE_MASSAGE');?></span>
					<?php /*
					if (!empty($this->storeinfo[0]->header) )
					{
					?>

						<div class="text-warning">
							<?php echo JText::_('COM_Q2C_EXISTING_IMAGE_MSG');?>
						</div>
						<div class="text-info">
							<?php echo JText::_('COM_Q2C_EXISTING_IMAGE');?>
						</div>
						<div>
							<?php
							//foreach($cdata['images'] as $img){
								echo "<img class='img-rounded com_qtc_header_img com_qtc_img_border' src='".JUri::root().$this->storeinfo[0]->header."' />";
							//}
							?>
						</div>
					<?php
					}*/
					?>
				</div>
			</div>
			-->

<!--     	/* @for now: On create store, hide def length,weight, tax ship details
			<hr class="hr hr-condensed"/>
-->

			<?php
			// Check for view override

			/*
			 * $taxshipPath = $comquick2cartHelper->getViewpath('vendor', 'taxship', "SITE", "SITE");
			ob_start();
			include($taxshipPath);
			$taxshipDetail = ob_get_contents();
			ob_end_clean();
			echo $taxshipDetail;
			*/
			?>

			<?php
			$paypalMode = " checked='checked' ";
			$otherMode = "";
			$display = " display:block;";
			$displaynone = " display:none;";
			$paypalEmailClass = " required ";

			// Means yes / 1
			if (!empty($this->storeinfo[0]->payment_mode))
			{
				$paypalMode = "";
				$otherMode = " checked='checked' ";
				$paypalEmailClass = '';
			}
			?>

			<hr class="hr hr-condensed"/>

			<!--PAYMENT mode paypal or other -->
			<div class="control-group">
				<div class="radio">
				<label for="paymentMode" class="control-label">
					<?php echo JHtml::tooltip(JText::_('VENDER_PAYMENT_MODE_TOOLTIP'), JText::_('PAYMENT_MODE'), '','* '. JText::_('PAYMENT_MODE'));?>
				</label>
				<div class="controls">
					<input type="radio" class="inputbox" <?php echo $paypalMode;?> value="0" id="paymentMode0" name="paymentMode" onclick="paymode(0)">
					<label class="radiobtn"  id="outofstockship0-lbl" for="paymentMode0"><?php echo JText::_('QTC_PAYPAL');?></label>

					<input type="radio" class="inputbox" <?php echo $otherMode;?> value="1" id="paymentMode1" name="paymentMode" onclick="paymode(1)">
					<label class="radiobtn" id="outofstockship1-lbl" for="paymentMode1"><?php echo JText::_('QTC_OTHER');?></label>
				</div>
				</div>
			</div>

			<!--for PAYPAL provide textbox for paypal email -->
			<?php $pay_details= !empty($this->storeinfo[0]->pay_detail) ? $this->storeinfo[0]->pay_detail : ''; ?>

			<div class="control-group" id="paypalmodeDiv" style="<?php echo (!empty($paypalMode)?$display:$displaynone); ?>" >
				<label for="paypalemail" class="control-label">
					<?php echo JHtml::tooltip(JText::_('VENDER_PAYPAL_EMAIL_TOOLTIP'), JText::_('PAYPAL_EMAIL'), '', '* '.JText::_('PAYPAL_EMAIL'));?>
				</label>
				<div class="controls">
					<input type="text" name="paypalemail" id="paypalemail" class="inputbox validate-email <?php echo $paypalEmailClass ;?>"  size="30" value="<?php echo !empty($paypalMode) ? $pay_details : ''; ?>" />
				</div>
			</div>

			<!--  IF OTHER PAYMENT METHOD -->
			<div class="control-group" id="othermodeDiv" style="<?php echo (!empty($otherMode)?$display:$displaynone); ?>" >
				<label for="otherPayMethod" class="control-label">
					<?php echo JHtml::tooltip(JText::_('VENDER_OTHER_PAY_METHOD_TOOLTIP'), JText::_('OTHER_PAY_METHOD'), '','* '. JText::_('OTHER_PAY_METHOD'));?>
				</label>
				<div class="controls">
					<textarea  size="28" rows="3" name="otherPayMethod" id="otherPayMethod" class="inputbox" ><?php if (!empty($this->storeinfo[0]->payment_mode)){ echo stripslashes($this->storeinfo[0]->pay_detail);}?></textarea>
				</div>
			</div>

			<?php
			// Trigger OnBeforeCreateStore
			if (!empty($this->OnBeforeCreateStore))
			{
				echo $this->OnBeforeCreateStore;
			}

			// Store limit msg
			$storeLimitPerUser = $qtc_params->get('storeLimitPerUser');

			if (!$mainframe->isAdmin() && !empty($storeLimitPerUser))
			{
				?>
				<div class="alert alert-info">
					<span><?php echo JText::sprintf('QTC_CRAETE_STORE_LIMIT_NOTE', $storeLimitPerUser); ?></span>
				</div>
				<?php
			}
			?>

			<div class="form-actions">
				<button type="button"
					title="<?php echo JText::_('BUTTON_SAVE_TEXT'); ?>"
					class="q2c-btn-wrapper btn btn-medium btn-success"
					onclick="qtcbuttonAction('vendor.save');" >
						<?php echo JText::_('BUTTON_SAVE_TEXT');?>
				</button>

				<button type="submit"
					class="q2c-btn-wrapper btn btn-medium btn-success"
					title="<?php echo JText::_('BUTTON_SAVE_AND_CANCEL')?>"
					onclick="qtcbuttonAction('vendor.saveAndClose');"/>
						<?php echo JText::_('BUTTON_SAVE_AND_CANCEL')?>
				</button>

				<button type="button"
					title="<?php echo JText::_('BUTTON_CANCEL_TEXT');?>"
					class="q2c-btn-wrapper btn btn-medium btn-danger"
					onclick="qtcbuttonAction('vendor.cancel');" >
						<?php echo JText::_( 'BUTTON_CANCEL_TEXT');?>
				</button>
			</div>
		</div>
		<!-- end main div-->
		<input type="hidden" name="option" value="com_quick2cart"/>
		<input type="hidden" name="task" value="vendor.save" />
		<input type="hidden" name="btnAction" value="saveAndClose" />
		<!-- by default saveAndClose -->
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="check" value="" />

		<?php $id=!empty($this->storeinfo)?$this->storeinfo[0]->id:''?>

		<input type="hidden" name="id" value="<?php echo $id;?>"/>

		<?php
		if (!empty($this->adminCall))
		{
			?>
			<input type="hidden" name="qtcadminCall" value="<?php echo $this->adminCall; ?>" />
			<?php
		}
		?>

		<?php echo JHtml::_( 'form.token' ); ?>

	</form>
</div>
