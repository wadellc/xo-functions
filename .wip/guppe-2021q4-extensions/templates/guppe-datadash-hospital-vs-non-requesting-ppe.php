<?php
/* 
 * Get Us PPE Data Dashboard - Hospital vs. Non.
 * Usage: [datadash-hospital-vs-non] 
 */


// add shortcode support
add_shortcode( 'datadash-hospital-vs-non', 'd3_hospital_v_non' );

//shortcode output
function d3_hospital_v_non() {
	ob_start();
?>

<div id="hvn" class="d3viz">
		<div class="page-wrapper">
			<div class="title">
				Facilities Requesting PPE
			</div>
			<div class="legend">
				<div class="legend-column hospitals">
					<div class="color-block"></div>
					<div class="text">
						Hospital Facilities
					</div>
				</div>
				<div class="legend-column non-hospitals">
					<div class="color-block"></div>
					<div class="text">
						Non-Hospital Facilities
					</div>
				</div>
			</div>
			<div id="requesting-ppe-chart" class="svg-container"></div>
			<div class="description">
				<b>Hover or tap each bar to see facility types per month.</b> The Get Us PPE request form asked requesters to identify their facility type. This graph displays requests logged in the Get Us PPE database from January 2021 through June 2021.
			</div>
		</div>
</div> <!-- /#hvn.d3viz -->



<?php 
	return ob_get_clean();
	}
?>