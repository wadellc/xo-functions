<?php
/* 
 * Get Us PPE Data Dashboard - PPE Requests by State.
 * Usage: [datadash-requests-by-state] 
 */


// add shortcode support
add_shortcode( 'datadash-requests-by-state', 'd3_ppe_requests_by_state' );

//shortcode output
function d3_ppe_requests_by_state() {
	ob_start();
?>

<!-- Viz 2 - Start Request By State -->
<div id="rbs" class="d3viz">

		<div id="request-map" class="container" style="margin:auto;">
			<div class="row page-wrapper">
				<div class="col-lg-9 col-12 map-wrapper">
					<div class="title">PPE Requests by State</div>
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
										<span class="color-text">200</span>
									</div>
									<div class="color-250">
										<span class="line"></span>
										<span class="color-text">400</span>
									</div>
									<div class="color-500">
										<span class="line"></span>
										<span class="color-text">600</span>
									</div>
									<div class="color-750">
										<span class="line"></span>
										<span class="color-text">800</span>
									</div>
									<div class="color-1000">
										<span class="line"></span>
										<span class="color-text">1000</span>
									</div>
									<div class="color-over"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-lg-12 col-12">
							<div class="national-stats">
								<div class="title">NATIONAL PPE REQUESTS</div>
								<div class="body">
									<div class="amount"></div>
									<div class="top-item">
										<div class="top-item-icon" style="display:flex; justify-content: center;">
											<img alt="" />
										</div>
										<div class="detail">
											<p class="top-item-title">TOP ITEM REQUESTED:</p>
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
										<span class="color-text">200</span>
									</div>
									<div class="color-250">
										<span class="line"></span>
										<span class="color-text">400</span>
									</div>
									<div class="color-500">
										<span class="line"></span>
										<span class="color-text">600</span>
									</div>
									<div class="color-750">
										<span class="line"></span>
										<span class="color-text">800</span>
									</div>
									<div class="color-1000">
										<span class="line"></span>
										<span class="color-text">1000</span>
									</div>
									<div class="color-over"></div>
								</div>
							</div>
							<div class="explaination">
								<b>Hover or tap each state to see requests per state.</b> This map displays the total number of active individual PPE requests per state through July 2, 2021. Numbers are derived from the Get Us PPE database of PPE requests.
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

</div> <!-- /#rbs.d3viz -->
<!-- End Request By State -->

<?php 
	return ob_get_clean();
	}
?>