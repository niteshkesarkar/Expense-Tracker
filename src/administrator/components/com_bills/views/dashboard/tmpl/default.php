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

$document = JFactory::getDocument();
$document->addScript(JUri::root(true) . '/administrator/components/com_bills/assets/js/chart.min.js');
$document->addScript(JUri::root(true) . '/administrator/components/com_bills/assets/js/custom.js');
$document->addStyleSheet(JUri::root(true) . '/media/techjoomla_strapper/bs3/css/bootstrap.min.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bills/assets/css/tjdashboard-sb-admin.css');

$document->addStylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
$this->sidebar = JHtmlSidebar::render();
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> q2c-admin-dashboard">
<?php if (!empty($this->sidebar)): ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
	<?php else: ?>
			<div id="j-main-container">
	<?php endif;?>
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<!-- TJ Bootstrap3 -->
		<div class="tjBs3">
			<!-- TJ Dashboard -->
			<div class="tjDB">
				<div id="wrapper">
					<div id="11111page-wrapper">
						<!-- /.row -->
						<div class="clearfix">&nbsp;</div>
						<div>
						<!-- Start - stat boxes -->
						<div class="row">
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-barcode fa-4x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge"><?php echo $this->total->expense; ?></div>
												<div>Total Expense</div>
											</div>
										</div>
									</div>
									<a href="<?php echo JRoute::_('index.php?option=com_bills&view=bills', false); ?>">
										<div class="panel-footer">
											<span class="pull-left">
												View Details											</span>
											<span class="pull-right">
												<i class="fa fa-arrow-circle-right"></i>
											</span>
											<div class="clearfix"></div>
										</div>
									</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-barcode fa-4x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge"><?php echo $this->count->groups; ?></div>
												<div>Total Groups</div>
											</div>
										</div>
									</div>
									<a href="<?php echo JRoute::_('index.php?option=com_bills&view=groups', false); ?>">
										<div class="panel-footer">
											<span class="pull-left">
												View Details											</span>
											<span class="pull-right">
												<i class="fa fa-arrow-circle-right"></i>
											</span>
											<div class="clearfix"></div>
										</div>
									</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-barcode fa-4x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge"><?php echo $this->count->types; ?></div>
												<div>Total Types</div>
											</div>
										</div>
									</div>
									<a href="<?php echo JRoute::_('index.php?option=com_bills&view=types', false); ?>">
										<div class="panel-footer">
											<span class="pull-left">
												View Details											</span>
											<span class="pull-right">
												<i class="fa fa-arrow-circle-right"></i>
											</span>
											<div class="clearfix"></div>
										</div>
									</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-barcode fa-4x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge"><?php echo $this->count->users; ?></div>
												<div>Total Users</div>
											</div>
										</div>
									</div>
									<a href="<?php echo JRoute::_('index.php?option=com_users&view=users', false); ?>">
										<div class="panel-footer">
											<span class="pull-left">
												View Details											</span>
											<span class="pull-right">
												<i class="fa fa-arrow-circle-right"></i>
											</span>
											<div class="clearfix"></div>
										</div>
									</a>
								</div>
							</div>
						<!-- End - stat boxes -->

						<div class="row">
							<div class="col-lg-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<i class="fa fa-pie-chart fa-fw"></i>
										<?php echo JText::_('Per User Groupwise Expense'); ?>
									</div>
									<div class="panel-body">
										<!-- CALENDER ND REFRESH BTN  -->
										<div class="clearfix row">
											<div class="col-sm-12 col-lg-12 col-md-12">
												<div class="pull-right">
													<div class="form-group">
														<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
															<div class="input-group">
																<?php echo $this->groupList1; ?>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearifx"></div>
										</div>
										<!--END::CALENDER ND REFRESH BTN  -->

										<div class="clearifx">&nbsp;</div>

										<!-- Periodic orders - graph start -->
										<div id="graph1">
											<canvas id="myChartPie1" width="250" style="heigh:300;width:300"></canvas>
										</div>
										<!-- Periodic orders - graph end -->
									</div>
									<!-- /.panel-body -->
								</div>
							</div>
							<!-- /.col-lg-4 -->
							<div class="col-lg-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<i class="fa fa-pie-chart fa-fw"></i>
										<?php echo JText::_('Per Type Groupwise Expense'); ?>
									</div>
									<div class="panel-body">
										<!-- CALENDER ND REFRESH BTN  -->
										<div class="clearfix row">
											<div class="col-sm-12 col-lg-12 col-md-12">
												<div class="pull-right">
													<div class="form-group">
														<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
															<div class="input-group">
																<?php echo $this->groupList2; ?>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearifx"></div>
										</div>
										<!--END::CALENDER ND REFRESH BTN  -->

										<div class="clearifx">&nbsp;</div>

										<!-- Periodic orders - graph start -->
										<div id="graph2">
											<canvas id="myChartPie2" width="250" style="heigh:300;width:300"></canvas>
										</div>
										<!-- Periodic orders - graph end -->
									</div>
									<!-- /.panel-body -->
								</div>
							</div>

							<div class="col-lg-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<i class="fa fa-pie-chart fa-fw"></i>
										<?php echo JText::_('Total Expense Groupwise'); ?>
									</div>
									<div class="panel-body">
										<!-- CALENDER ND REFRESH BTN  -->
										<div class="clearfix row">
											<div class="col-sm-12 col-lg-12 col-md-12">
												<br/>
												<br/>
											</div>
										</div>
										<!-- Periodic orders - graph start -->
										<div id="graph3">
											<canvas id="myChartPie3" width="250" style="heigh:300;width:300"></canvas>
										</div>
										<!-- Periodic orders - graph end -->
									</div>
									<!-- /.panel-body -->
								</div>
							</div>
						</div>
					</div>
					<!-- /#page-wrapper -->
				</div>
			</div>
			<!-- /.tjDB TJ Dashboard -->
		</div>
		<!-- /.tjBs3TJ TJ Bootstrap3 -->
	</form>
</div>

<script>
// For a pie chart
var ctx1 = document.getElementById("myChartPie1");
var ctx2 = document.getElementById("myChartPie2");
var ctx3 = document.getElementById("myChartPie3");

var myPieChart1 = new Chart(ctx1,{type:"pie",data:{labels:["No data show. Use the filter."],datasets:[{data:[100],backgroundColor:["#337ab7"],hoverBackgroundColor:["#337ab7"]}]},options:{animation:{animateScale:!0},legend:{display:false}}});

var myPieChart2 = new Chart(ctx2,{type:"pie",data:{labels:["No data show. Use the filter."],datasets:[{data:[100],backgroundColor:["#337ab7"],hoverBackgroundColor:["#337ab7"]}]},options:{animation:{animateScale:!0},legend:{display:false}}});

var myPieChart3 = new Chart(ctx3,{type:"pie",data:{labels:["No data show. Use the filter."],datasets:[{data:[100],backgroundColor:["#337ab7"],hoverBackgroundColor:["#337ab7"]}]},options:{animation:{animateScale:!0},legend:{display:false}}});

	billsJs.dashboard.dashboardInit();
</script>