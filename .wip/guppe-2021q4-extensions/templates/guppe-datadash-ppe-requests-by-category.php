<?php
/* 
 * Get Us PPE Data Dashboard - Requests by Category.
 * Usage: [datadash-requests-by-category] 
 */


// add shortcode support
add_shortcode( 'datadash-requests-by-category', 'd3_categorical_requests' );

//shortcode output
function d3_categorical_requests() {
	ob_start();
?>

<div id="rbc" class="d3viz">
		<div class="container" style="margin:auto;">
			<!--row-->
			<div class="row">
				<div class="col-sm-12 text-center mt-5">
					<h2>Requests for Facial PPE</h2>
				</div>
				<!--col-sm-12-->
			</div>
			<!--row-->
			<div class="row chart-wrapper" id="container1">
				<div class="chart-body no-left-padding">
					<div id="container1-chart" data-toggle="popover" data-placement="left" class="chart"></div>
				</div>
				<!--col-lg-8-->
				<div class="chart-legend">
					<ul class="legends d-block">
						<li>
							<small class="text-capitalize float-left">PPE TYPE</small>
							<small class="text-capitalize float-right">6 MONTH TOTAL</small>
							<div class="clearfix"></div>
							<!--clearfix-->
						</li>
					</ul>
				</div>
				<!--col-lg-4-->
			</div>
			<!--row-->

			<!--row-->
			<div class="row">
				<div class="col-sm-12 text-center mt-5">
					<h2>Requests for Non-Facial PPE</h2>
				</div>
				<!--col-sm-12-->
			</div>
			<!--row-->
			<div class="row chart-wrapper" id="container2">
				<div class="chart-body no-left-padding">
					<div id="container2-chart" data-toggle="popover" data-placement="left" class="chart"></div>
				</div>
				<!--col-lg-8-->
				<div class="chart-legend">
					<ul class="legends d-block">
						<li>
							<small class="text-capitalize float-left">PPE TYPE</small>
							<small class="text-capitalize float-right">6 MONTH TOTAL</small>
							<div class="clearfix"></div>
							<!--clearfix-->
						</li>
					</ul>
				</div>
				<!--col-lg-4-->
			</div>
			<!--row-->

			<!--row-->
			<div class="row">
				<div class="col-sm-12 text-center mt-5">
					<h2>Requests for Sanitizing & Other Types of PPE</h2>
				</div>
				<!--col-sm-12-->
			</div>
			<!--row-->
			<div class="row chart-wrapper" id="container3">
				<div class="chart-body no-left-padding">
					<div id="container3-chart" data-toggle="popover" data-placement="left" class="chart"></div>
				</div>
				<!--col-lg-8-->
				<div class="chart-legend">
					<ul class="legends d-block">
						<li>
							<small class="text-capitalize float-left">PPE TYPE</small>
							<small class="text-capitalize float-right">6 MONTH TOTAL</small>
							<div class="clearfix"></div>
							<!--clearfix-->
						</li>
					</ul>
				</div>
				<!--col-lg-4-->
			</div>
			<!--row-->
		</div>
		<!--container-->
</div> <!-- /#rbc.d3viz -->

<?php 
	return ob_get_clean();
	}
?>