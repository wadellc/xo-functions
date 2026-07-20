<?php
/* 
 * Get Us PPE Data Dashboard - Delivered vs. Requested
 * Usage: [datadash-delivered-v-requested] 
 */


// add shortcode support
add_shortcode( 'datadash-delivered-v-requested', 'd3_delivered_requested' );

//shortcode output
function d3_delivered_requested() {
	ob_start();
?>

<div id="dvr" class="d3viz">
		<div class="container">
			<!-- <div class="row"> -->
				<div class="page-wrapper">
					<div class="title">PPE Delivered vs. Requested</div>
					<div class="sub-title">
						<span class="text-uppercase"># DELIVERED</span>
						<span class="text-uppercase"># REQUESTED</span>
					</div>
					<div class="main"></div>
					<div class="description">
						This visual displays total number of PPE units delivered compared to number of units requested through July 2, 2021. Numbers are derived from the Get Us PPE database.
					</div>
				</div>
			<!-- </div> -->
		</div> <!-- /container-->
</div> <!-- /#dvr.d3viz -->

<?php 
	return ob_get_clean();
	}
?>