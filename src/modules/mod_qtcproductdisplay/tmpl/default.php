<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_quick2cart/defines.php';

$lang = JFactory::getLanguage();
$lang->load('mod_qtcproductdisplay', JPATH_ROOT);
$comparams = JComponentHelper::getParams('com_quick2cart');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_quick2cart/assets/css/quick2cart.css' );
$comquick2cartHelper = new comquick2cartHelper();
?>
<div class="<?php echo Q2C_WRAPPER_CLASS . ' ' . $params->get('moduleclass_sfx'); ?>" >
	<div class=''>

		<?php
		$mod_mode = "_" . $module_mode;
		$random_container = 'q2c_pc_mod_products_display' .  $mod_mode;?>

		<div id="q2c_pc_mod_products_display<?php echo  $mod_mode?>">
			<?php
				$layout_to_load = $params->get('layout_to_load','flexible_layout','string');
				$pinHeight = $params->get('fix_pin_height','200','int');
				$noOfPin_lg = $params->get('pin_for_lg','3','int');
				$noOfPin_md = $params->get('pin_for_md','3','int');
				$noOfPin_sm = $params->get('pin_for_sm','4','int');
				$noOfPin_xs = $params->get('pin_for_xs','2','int');

				$Fixed_pin_classes = "";

				if ($layout_to_load == "fixed_layout")
				{
					$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md. " col-lg-" . $noOfPin_lg . " ";
				}
			?>
			<?php
			foreach($target_data as $data)
			{
			?>
				<div class="q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes;?>">
				<?php
				$path = JPATH_SITE . '/components/com_quick2cart/views/product/tmpl/product.php';
				ob_start();
				include($path);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
				?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php
// Get pin width
$pin_width = $params->get('pin_width');

if (empty($pin_width))
{
	$pin_width = 170;
}

// Get pin padding
$pin_padding = $params->get('pin_padding');

if (empty($pin_padding))
{
	$pin_padding = 7;
}

// Calulate columnWidth (columnWidth = pin_width+pin_padding)
$columnWidth = $pin_width + $pin_padding;
?>
<?php
	if ($layout_to_load == "flexible_layout")
	{
?>
	<style type="text/css">
		.q2c_pin_item_<?php echo $random_container;?> { width: <?php echo $pin_width . 'px'; ?> !important; }
	</style>

	<script type="text/javascript">
		var pin_container_<?php echo $random_container; ?> = "q2c_pc_mod_products_display<?php echo  $mod_mode?>";
		var columnWidth_<?php echo $random_container; ?> = <?php echo $columnWidth; ?>;
		var random_container_<?php echo $random_container; ?> = '.q2c_pin_item_<?php echo $random_container;?>';
		var pin_padding_<?php echo $random_container; ?> = <?php echo $pin_padding; ?>;

		techjoomla.jQuery(document).ready(function()
		{
			QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 100);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 1000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 2000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 3000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 4000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 5000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 5000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 8000);

			setTimeout(function() { QttPinArrange(pin_container_<?php echo $random_container; ?>, columnWidth_<?php echo $random_container; ?>, random_container_<?php echo $random_container; ?>, pin_padding_<?php echo $random_container; ?>); }, 10000);
		});
	</script>
	<?php
	}
	?>
