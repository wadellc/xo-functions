<?php
/* 
 * Get Us PPE Data Dashboard - PPE Supply Remaining.
 * Usage: [datadash-ppe-supply-remaining] 
 */


// add shortcode support
add_shortcode( 'datadash-ppe-supply-remaining', 'd3_ppe_supply_remaining' );

//shortcode output
function d3_ppe_supply_remaining() {
	ob_start();
?>

<div id="psr" class="d3viz">
				<div class="page-wrapper">
					<div class="title">N95 Masks</div>
					<div class="sub-title">Days remaining until requesters run out</div>
					<div class="chart-area">
						<div class="chart n95-masks">
							<div class="out-ppe">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> Out of PPE </span>
							</div>
							<div class="more-7">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> &lt; 7 days </span>
							</div>
							<div class="less-7">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> &geq; 7 days </span>
							</div>
						</div>
					</div>
					<div class="title">Nitrile Gloves</div>
					<div class="sub-title">Days remaining until requesters run out</div>
					<div class="chart-area">
						<div class="chart nitrile-gloves">
							<div class="out-ppe">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> Out of PPE </span>
							</div>
							<div class="more-7">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> &lt; 7 days </span>
							</div>
							<div class="less-7">
								<span class="percentage"></span>
								<span class="line"></span>
								<span class="label"> &geq; 7 days </span>
							</div>
						</div>
					</div>
					<div class="explaination">
						Each facility making a request for PPE estimated how long their remaining supply would last. This visualization includes requests logged in the Get Us PPE database through July 2, 2021.
					</div>
				</div>
</div> <!-- /#psr.d3viz -->

<?php 
	return ob_get_clean();
	}
?>