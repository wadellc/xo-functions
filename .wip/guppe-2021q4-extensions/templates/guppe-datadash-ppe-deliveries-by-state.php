<?php
/* 
 * Get Us PPE Data Dashboard - PPE Deliveries by State.
 * Usage: [datadash-deliveries-by-state] 
 */


// add shortcode support
add_shortcode( 'datadash-deliveries-by-state', 'd3_ppe_deliveries_by_state' );

//shortcode output
function d3_ppe_deliveries_by_state() {
	ob_start();
?>

<!-- Viz 8 - Start Deliveries By State -->
<div id="dbs" class="d3viz">

		<div id="delivery-map" class="container" style="margin:auto;">
			<div class="row page-wrapper">
				<div class="col-lg-9 col-12 map-wrapper">
					<div class="title">PPE Deliveries by State</div>
					<div class="map">
						<div class="svg-container">
							<svg id="my_dataviz" class="svg-content-responsive"></svg>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-12 detail-wrapper">
					<div class="row">
						<div class="col-12 d-block d-md-none">
							<div class='color-area'>
								<div class="bar">
									<div class="color-100">
										<span class="line"></span>
										<span class="color-text">25k</span>
									</div>
									<div class="color-250">
										<span class="line"></span>
										<span class="color-text">50k</span>
									</div>
									<div class="color-500">
										<span class="line"></span>
										<span class="color-text">100k</span>
									</div>
									<div class="color-750">
										<span class="line"></span>
										<span class="color-text">250k</span>
									</div>
									<div class="color-1000">
										<span class="line"></span>
										<span class="color-text">500k</span>
									</div>
									<div class="color-over"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-lg-12 col-12">
							<div class="national-stats">
								<div class="title">NATIONAL PPE DELIVERIES</div>
								<div class="body">
									<div class="amount"></div>
									<div class="top-item">
										<div class="top-item-icon" style="display:flex; justify-content: center;">
											<img alt="" />
										</div>
										<div class="detail">
											<p class="top-item-title">TOP ITEM DELIVERED:</p>
											<p class="top-item-name"></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-lg-12 col-12">
							<div class="color-area d-none d-md-block">
								<div class="bar">
									<div class="color-100">
										<span class="line"></span>
										<span class="color-text">25k</span>
									</div>
									<div class="color-250">
										<span class="line"></span>
										<span class="color-text">50k</span>
									</div>
									<div class="color-500">
										<span class="line"></span>
										<span class="color-text">100k</span>
									</div>
									<div class="color-750">
										<span class="line"></span>
										<span class="color-text">250k</span>
									</div>
									<div class="color-1000">
										<span class="line"></span>
										<span class="color-text">500k</span>
									</div>
									<div class="color-over"></div>
								</div>
							</div>
							<div class="explaination">
								<b>Hover or tap each state to see deliveries per state.</b> This map displays the total number of PPE units delivered per state through July 2, 2021. Numbers are derived from the Get Us PPE database of donated PPE deliveries.
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

</div> <!-- /#dbs.d3viz -->
<!-- End Deliveries By State -->

<?php 
	return ob_get_clean();
	}
?>