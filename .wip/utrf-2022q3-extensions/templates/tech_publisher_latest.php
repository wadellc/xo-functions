<?php
/* 
 * UTRF Technology Publisher Latest Technoloigies
 * Usage: [technology_latest] 
 */

add_shortcode( 'technology_latest', 'inteum_latest_technologies' );

function inteum_latest_technologies() {
	ob_start();
?>
<!-- -- Latest Technologies -- -->
		<div class="latest_tech_wrap">
			<h3>Latest Technologies</h3>
			<div class="inteum_widget ltp">
				<!--- Inteum Latest Technologies Posted 'Web Part' -->
				<script id="scriptlatesttechnologiesposted" src="//utrf.technologypublisher.com/widget.aspx?type=ltp&amp;num=5&amp;list=v&amp;space=5" type="text/javascript"></script>
			</div>
		</div>
<?php
	return ob_get_clean();
}
?>