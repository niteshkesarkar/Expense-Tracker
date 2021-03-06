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

jimport('joomla.html.pane');
// JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$jinput = JFactory::getApplication()->input;
$params = JComponentHelper::getParams('com_quick2cart');
$qtc_base_url = JUri::base();

$entered_numerics= "'".JText::_('QTC_ENTER_NUMERICS')."'";
//$document = JFactory::getDocument();
//$currencies=$params->get('addcurrency');
$addpre_select = array();
$addpre_select[] = JHtml::_('select.option','+', JText::_('QTC_ADDATTRI_PREADD'));
$addpre_select[] = JHtml::_('select.option','-', JText::_('QTC_ADDATTRI_PRESUB'));
//$addpre_select[] = JHtml::_('select.option','=', JText::_('QTC_ADDATTRI_PRESAM'));

if (version_compare(JVERSION, '1.6.0', 'ge'))
	$js_key="
	Joomla.submitbutton = function(task){ ";
else
	$js_key="
	function submitbutton( task ){";

	$js_key.="
		if (task == 'cancel')
		{";
	        if (version_compare(JVERSION, '1.6.0', 'ge'))
				$js_key.="Joomla.submitform(task);";
			else
				$js_key.="document.qtcAddProdForm.submit();";
	    $js_key.="
	    }else{
			var validateflag = document.formvalidator.isValid(document.qtcAddProdForm);
			if (validateflag){";
				if (version_compare(JVERSION, '1.6.0', 'ge'))
				{
					$js_key.="
				Joomla.submitform(task);";
				}else{
					$js_key.="
				document.qtcAddProdForm.submit();";
				}
			$js_key.="
			}else{
				return false;
			}
		}
	}
	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
		   if (el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) { alert(\"".JText::_('QTC_NUMERICS_NOT_ALLOWED')."\"); el.value = el.value.substring(0,i); break;}
		}
	}


/*
function removeopt(elem,id){
	var opt_id = techjoomla.jQuery('input[name=\"attri_opt[' + id + '][id]').val();
	if (opt_id){
	var confirm = confirm('Do you want to remove this option?');
	if (confirm){
		techjoomla.jQuery.ajax({
			url: '".$qtc_base_url."/index.php?option=com_quick2cart&controller=cartcheckout&task=delattributeoption&tmpl=component&opt_id='+opt_id,
			type: 'GET',
			success: function(msg)
			{
				window.location.reload();
			}
		});
	}
}
techjoomla.jQuery(elem).parent().remove();

}
function saveAttributeOptionCurrency(currdata,pid)
	{
		var currvalue='';
		techjoomla.jQuery('.currtext').each(function() {
			var bval = techjoomla.jQuery(this).val();
			var bid = techjoomla.jQuery(this).attr('id');
			currvalue+=bval+',';

		});
	}*/
function qtc_ispositive(ele)
{
		var val=ele.value;
		if (val==0 || val < 0)
		{
			ele.value='';
			alert(\"".JText::_('QTC_ENTER_POSITIVE_ORDER')."\");
			return false;
		}

	}

";
	$document->addScriptDeclaration($js_key);
	$addpre_select = array();
	$addpre_select[] = JHtml::_('select.option','+', JText::_('QTC_ADDATTRI_PREADD'));
	$addpre_select[] = JHtml::_('select.option','-', JText::_('QTC_ADDATTRI_PRESUB'));
	//$addpre_select[] = JHtml::_('select.option','=', JText::_('QTC_ADDATTRI_PRESAM'));


	$pid =  $jinput->get('pid',0,'INTEGER');
	$client =  $jinput->get( 'client','','STRING');
	$attri_model=new quick2cartModelAttributes();

	// clearing previous contain(garbage)
	$this->attribute_opt=array();
	if (!empty($this->allAttribues[$i]))
	{
		$this->attribute_opt = $attri_model->getAttributeoption($this->allAttribues[$i]->itemattribute_id);
		$att_op_count=count($this->attribute_opt);
	}
	else
	{
		$att_op_count=0;
	}

	//  For field type and is compulsory field
	$hideFieldsForStockableAttribute = (!empty($stockableAttrId) && $stockableAttrId == $this->allAttribues[$i]->itemattribute_id) ? "display:none;" : '';
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 ">
			<table class="table  table-condensed removeMargin qtc-table qtc-noborder" >
<!--
			<thead>
				<tr>
					<th width="33%" align="left"><?php echo JText::_( 'QTC_ADDATTRI_NAME' ); ?> </th>
					<th width="33%"	align="left"><?php echo JText::_( 'QTC_ADDATTRI_FIELD_TYPE_TO_USE' ); ?></th>
					<th width="%"	align="left"><?php echo JText::_( 'QTC_ATT_COMPALSARY_CK' ); ?> </th>
				</tr>
			</thead>
-->
			<tbody>
				<tr class="attributeTrHeading hidden-xs hidden-sm">
					<td width="33%" align="left">
						<label class="" title="<?php echo JText::_('QTC_PROD_SEL_ATTRIBUTE_TOOLTIP');?>" ><b><?php echo JText::_('QTC_PROD_SEL_ATTRIBUTE'); ?></b></label>
					</td>
					<td width="33%"	align="left"></td>
					<td align="left">
						<b  class="<?php echo $stockCheckboxClass; ?>" title="<?php echo JText::_('COM_QUICK2CART_IS_STOCKABLE_UNIT_DESC');?>" ><?php echo JText::_('COM_QUICK2CART_IS_STOCKABLE_UNIT')?> </b>
					</td>
				</tr>
				<tr class="attributeTrHeading">
					<td width="33%" align="left" data-title="<?php echo JText::_('QTC_PROD_SEL_ATTRIBUTE');?>">
						<?php
						$selectedAttrSet = !empty($this->allAttribues[$i]->global_attribute_id) ? $this->allAttribues[$i]->global_attribute_id : "0";
						echo JHtml::_('select.genericlist', $attributeSetOptions, "att_detail[". $i . "][global_attribute_set]",'class="qtcAttriSetType" sutocomplete="off" style="" onChange="qtcLoadArribute(this.id, ' . $i . ', qtcJsData)" ','value','text',$selectedAttrSet, "qtcAttriSetTypeId" + $i);
						?>
						<span class="attri_ajax_loading_<?php echo $i; ?>" style="display:none;">
							<img class="" src="<?php echo JUri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
						</span>
					</td>
					<td class="hidden-xs hidden-sm" width="33%"	align="left"></td>
					<td	align="left" data-title="<?php echo JText::_('COM_QUICK2CART_IS_STOCKABLE_UNIT');?>" class="<?php echo ($stockableAttrId || $qtc_stck_att  || $productType != 2) ? "qtc_hideEle" : ''?>">
						<?php
						$qtc_stck_att="";

						if (isset($this->allAttribues[$i]->is_stock_keeping) && $this->allAttribues[$i]->is_stock_keeping == 1)
						{
							$qtc_stck_att = ($this->allAttribues[$i]->is_stock_keeping) ? "checked" : "";
						}
						?>
						<!-- // (SHOW only varible product) OR If any of product attribute has one of stockable attribute then don't show checkbox  -->
						<label class="qtcAttrCheckbox  " title="<?php echo JText::_('COM_QUICK2CART_IS_STOCKABLE_UNIT_DESC');?>">

							<input type="checkbox" class=" qtc_is_stock_keeping <?php echo ($qtc_stck_att == "checked") ? "qtc_hideEle" : ''?>" name="att_detail[<?php echo $i; ?>][is_stock_keeping]" autocomplete="off" <?php echo $qtc_stck_att;?> onchange="qtcOnChange_is_stock_keeping(this)">
						</label>

						<?php
							// Edit view: for stockable attribute-> hide checkbod and show badges
						if(($qtc_stck_att == "checked"))
						{
							?>
							<span class="label label-success"><?php echo JText::_("COM_QUICK2CART_STOCK_KEEPING_ATTR_BEDGE");?></span>

							<?php
						}
					?>
					</td>
				</tr>
				<tr class="hidden-xs hidden-sm">
					<td width="33%" align="left">
						<b class="qtcAttriTabLabel"><?php echo JText::_( 'QTC_ADDATTRI_NAME' ); ?></b>
					</td>
					<td width="33%"	align="left">
						<b class="qtcfieldTypeTitle" style="<?php echo $hideFieldsForStockableAttribute ?>"><?php echo JText::_( 'QTC_ADDATTRI_FIELD_TYPE_TO_USE' ); ?></b>
					</td>
					<td align="left" style="<?php echo $hideFieldsForStockableAttribute ?>"
						<b class="checkboxdivTitle" >
						<?php echo JText::_( 'QTC_ATT_COMPALSARY_CK' ); ?> </b>
					</td>
				</tr>
			<tr>
				<td data-title="<?php echo JText::_('QTC_ADDATTRI_NAME');?>">
					<input id="atrri_name" name="att_detail[<?php echo $i; ?>][attri_name]" class="input-medium bill qtc_attrib " type="text" value="<?php echo (isset($this->allAttribues[$i]->itemattribute_name)) ? htmlentities($this->allAttribues[$i]->itemattribute_name) : ''; ?>" maxlength="250"  title="<?php echo JText::_('QTC_ADDATTRI_NAME_DESC')?>">
					<!-- Hidded field -->
					<input id="atrri_name_id" name="att_detail[<?php echo $i; ?>][attri_id]" class="input-medium bill " type="hidden" value="<?php echo (isset($this->allAttribues[$i]->itemattribute_id))?$this->allAttribues[$i]->itemattribute_id:''; ?>" maxlength="250"  title="<?php echo JText::_('QTC_ADDATTRI_NAME_DESC')?>">
				</td>
				<td data-title="<?php echo JText::_('QTC_ADDATTRI_FIELD_TYPE_TO_USE');?>" style="<?php echo $hideFieldsForStockableAttribute ?>">
					<?php
					$fields = array();
					$default =  !empty($this->allAttribues[$i]->attributeFieldType)? $this->allAttribues[$i]->attributeFieldType :"Select";
					$tableDisplay ='display:table';

					if ($default == 'Textbox')
					{
						$tableDisplay ='display:none';
					}

					$fields = $this->productHelper->getAttributeFieldsOption();
					$fnparam = "this,'".$attribute_container_id."'";
					?>

					<div >
						<?php
							echo JHtml::_('select.genericlist', $fields, "att_detail[$i][fieldType]", 'class="no_chzn qtcfieldType input-small" data-chosen="qtc" onChange="qtc_fieldTypeChange('.$fnparam.')"', "value", "text",$default);
						?>
					</div>
				</td>
				<td data-title="<?php echo JText::_('QTC_ATT_COMPALSARY_CK');?>" style="<?php echo $hideFieldsForStockableAttribute ?>">
					<label class="qtcAttrCheckbox" >
					<?php
						$qtc_ck_att="";

						if (isset($this->allAttribues[$i]->attribute_compulsary) && $this->allAttribues[$i]->attribute_compulsary == 1)
						{
							$qtc_ck_att=($this->allAttribues[$i]->attribute_compulsary)?"checked":"";
						}
					?>
					<input type="checkbox" class="checkboxdiv " name="att_detail[<?php echo $i; ?>][iscompulsary_attr]" autocomplete="off" <?php echo $qtc_ck_att;?> >
				</label>

				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
	<?php
	$k = 1;
	$lastkey_opt = $att_op_count ;//count($this->attribute_opt);
	?>
	<div class="qtcMarginTop10px">
		<table class="table table-condensed removeMargin qtc_attributeOpTable qtc-table " style="<?php echo $tableDisplay; ?>"><!-- the table width is fixed to 450px -->
			<thead >
				<tr >
					<th width="20%"><?php echo JText::_( 'QTC_ADDATTRI_OPTNAME' ); ?> </th>

					<th width="100px" align="left"><?php echo JText::_('QTC_PROD_STATUS'); ?> </th>

					<th width="7%" class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" ><?php echo JText::_('QTC_ADDATTRI_STOCK'); ?></th>
					<th width="10%" class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields"><?php echo JText::_('QTC_ADDATTRI_SKU'); ?></th>
<!--
					<th width="7%"><?php echo JText::_( 'QTC_ADDATTRI_OPTPREFIX' ); ?></th>
-->
					<th ><?php echo JText::_( 'QTC_ADDATTRI_OPTVAL' ); ?> </th>
					<th width="10%"><?php echo JText::_( 'QTC_ADDATTRI_OPTORDER' ); ?></th>
					<th width="5%" ></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$showAddBtn = 1;

			if (!empty($this->allAttribues[$i]->itemattribute_id))
			{
				/* Dont show add btn for global attibure set or stockable  attribute */
				// (!empty($stockableAttrId) && $stockableAttrId === $this->allAttribues[$i]->itemattribute_id))
				if ($selectedAttrSet)
				{
					// Don't generate last option for stock keeping attibure
					$att_op_count = $att_op_count -1;
					$showAddBtn = 0;
				}
			}

			for ($k = 0; $k <=$att_op_count ; $k++)
			{
				$childProdDetail = !empty($this->attribute_opt[$k]->child_product_detail) ? $this->attribute_opt[$k]->child_product_detail : new stdClass;
				echo '';
			?>
			<tr class="form-inline clonedInput" id="attri_opts<?php echo $k; ?>" >
				<td data-title="<?php echo JText::_('QTC_ADDATTRI_OPTNAME');?>">
					<input type="hidden" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][id]" value="<?php echo (!empty($this->attribute_opt[$k]->itemattributeoption_id))?$this->attribute_opt[$k]->itemattributeoption_id:''; ?>">
					<input type="hidden" class="qtcGlobalOptionId" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][globalOptionId]" value="<?php echo (!empty($this->attribute_opt[$k]->global_option_id)) ? $this->attribute_opt[$k]->global_option_id:''; ?>">
					<input type="hidden" class="" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][child_product_item_id]" value="<?php echo (!empty($this->attribute_opt[$k]->child_product_item_id)) ? $this->attribute_opt[$k]->child_product_item_id:''; ?>">
					<input type="text" class="input-small qtcOptionNameClass" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][name]" placeholder="<?php echo JText::_('QTC_ADDATTRI_OPTNAME')?>" value="<?php echo (isset($this->attribute_opt[$k]->itemattributeoption_name))? htmlentities($this->attribute_opt[$k]->itemattributeoption_name):''; ?>">
				</td>

				<td data-title="<?php echo JText::_('QTC_PROD_STATUS');?>">
					<?php
					$option_status = array();
					$option_status[] = JHtml::_('select.option',1, JText::_('QTC_PROD_PUBLISH'));
					$option_status[] = JHtml::_('select.option',0, JText::_('QTC_PROD_UNPUBLISH'));

					$defOptionStatus = (isset($this->attribute_opt[$k]->state))?$this->attribute_opt[$k]->state: 1;
					echo JHtml::_('select.genericlist', $option_status, "att_detail[$i][attri_opt][$k][state]", 'class="chzn-done qtc-attribute-option-state"', "value", "text", $defOptionStatus);
					?>
				</td>

				<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" data-title="<?php echo JText::_('QTC_ADDATTRI_STOCK');?>">
					<!-- For stockable attribute - sku and stock field -->
					<input type="text" class="qtcAttriOptionStock" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][stock]" Onkeyup="" onchange=""	 id=""   placeholder="<?php echo JText::_('QTC_ADDATTRI_STOCK')?>" value="<?php echo isset($childProdDetail->stock) ? $childProdDetail->stock : '' ?>">
				</td>
				<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" data-title="<?php echo JText::_('QTC_ADDATTRI_SKU');?>">
					<input type="text" class="qtcAttriOptionSku" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][sku]" Onkeyup="" onchange=""	 id="" class=""  placeholder="<?php echo JText::_('QTC_ADDATTRI_SKU')?>" value="<?php echo !empty($childProdDetail->sku) ? $childProdDetail->sku : '' ?>">
				</td>
				<td data-title="<?php echo JText::_('QTC_ADDATTRI_OPTVAL');?>">
					<?php

					$addpre_val = (isset($this->attribute_opt[$k]->itemattributeoption_prefix))?$this->attribute_opt[$k]->itemattributeoption_prefix:'';
					echo JHtml::_('select.genericlist', $addpre_select, "att_detail[$i][attri_opt][$k][prefix]", 'class="chzn-done qtc-attribute-option-prefix" data-chosen="qtc"', "value", "text", $addpre_val);
					$currencies=$params->get('addcurrency');
					$curr=explode(',',$currencies);
					$currencies_sym=$params->get('addcurrency_sym');

					if (!empty($currencies_sym) )
					{
						$curr_syms=explode(',',$currencies_sym);
					}
					?>
					<div class='qtc_currencey_textbox'>
						<?php $quick2cartModelAttributes =  new quick2cartModelAttributes();

							foreach($curr as $currKey=>$value)    // key contain 0,1,2... // value contain INR...
							{
									$currvalue=array();
									$storevalue="";

									if (isset($this->attribute_opt[$k] ))
									{
										$currvalue=$quick2cartModelAttributes->getOption_currencyValue($this->attribute_opt[$k]->itemattributeoption_id,$value);
										$storevalue=(isset($currvalue[0]['price']))?$currvalue[0]['price'] : '';
									}

									if (!empty($curr_syms[$currKey]))
									{
										$currtext = $curr_syms[$currKey];
									}
									else
									{
										$currtext = $value;
									}
									?>
								<div class="input-group curr_margin " >
									<input type='text' name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][currency][<?php echo $value; ?>]"  id='' value="<?php echo ((isset($currvalue[0]['price']))?$currvalue[0]['price'] : ''); ?>" class="col-md-2 currtext form-control" Onkeyup="checkforalpha(this,46,<?php echo $entered_numerics; ?>);">
									<div class="input-group-addon "><?php echo $currtext; ?></div>
								</div>
						<?php
							}
						?>
					</div>
				</td>
				<td data-title="<?php echo JText::_('QTC_ADDATTRI_OPTORDER');?>">
					<input type="text" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][order]" Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>);" onchange="qtc_ispositive(this)"	 id="" class="qtc-attribute-Option-order"  placeholder="<?php echo JText::_('QTC_ADDATTRI_OPTORDER')?>" value="<?php echo (isset($this->attribute_opt[$k]->ordering))?$this->attribute_opt[$k]->ordering:1; ?>">
				</td>
				<td class="qtcAddRmBTD">
				<?php

				/* Dont show add btn for stockable attribute */
				if ($k == $lastkey_opt && $showAddBtn)
				{ ?>
					 <button type="button"  class="btnAdd btn btn-mini btn-primary" id="<?php echo "qtc_container". $i;?>" title="<?php echo JText::_('QTC_ADD_MORE_OPTION_TITLE')?>" onclick="addopt(this.id);"><i class="<?php echo QTC_ICON_PLUS;?> icon22-white22"></i></button>
					<?php
				}
				else
				{
					?>
					 <button type="button" class="btn btn-mini btn-danger qtcRemoveOption" id="btnRemove<?php echo $k; ?>" title="<?php echo JText::_('QTC_REMOVE_MORE_OPTION_TITLE');?>" onclick="techjoomla.jQuery(this).closest('tr').remove();" >
					 <i class="<?php echo Q2C_ICON_TRASH;?> icon22-white22"></i>
					 </button>
					<?php
				}
				?>
				</td>
			</tr>

			<?php } ?>

			</tbody>
		</table>

		<div class="">
			<?php
				$hideAddGlobalOptionFields = 1;

				if ($selectedAttrSet && $this->allAttribues[$i]->global_attribute_id)
				{
					$hideAddGlobalOptionFields = 0;
				}
			?>
			<div class="qtcGlobalOptionsList" style="<?php echo !empty($hideAddGlobalOptionFields) ? "display:none;" : '' ?>">
				<?php
				// Get global options
				$goptions = $this->productHelper->getGlobalAttriOptions($this->allAttribues[$i]->global_attribute_id);

				// Generate option select box
				$layout = new JLayoutFile('addproduct.attribute_global_options');
				echo $layout->render($goptions);
				?>

			</div>
		</div>
	</div>
</div><!---row fluid end -->



	<?php echo JHtml::_( 'form.token' ); ?>
<!-- </form> -->

