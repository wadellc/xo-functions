<?php
/* 
 * Get Us PPE Data Dashboard - Most Requested Types
 * Usage: [datadash-most-requested] 
 */


// add shortcode support
add_shortcode( 'datadash-most-requested', 'd3_most_requested' );

//shortcode output
function d3_most_requested() {
	ob_start();
?>

<div id="mrq" class="d3viz">
<!-- 		<div class="container">
			<div class="row">
				<div class="col-md-6"> -->
					<div class="page-wrapper">
						<div class="title">Most Requested Types of PPE</div>

						<div class="btn-group">
							<button onclick="displayData('allTime')" class="left">Mar 2020 - Jun 2021</button>
							<button onclick="displayData('june2021')" class="right">June 2021</button>
						</div>

						<div class="images mrq"></div>

						<div class="description">
							All time data reflects the median request size of all PPE requests logged in the Get Us PPE database from March 2020 through June 2021. The June 2021 timeframe is provided for comparison of how PPE types requested changed throughout the pandemic.
						</div>
					</div>
<!-- 				</div>
			</div>
		</div> -->
</div> <!-- /#mrq.d3viz -->

<?php 
	return ob_get_clean();
	}
?>