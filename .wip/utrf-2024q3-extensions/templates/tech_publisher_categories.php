<?php
/* 
 * UTRF Technology Publisher Categories
 * Usage: [technology_categories] 
 */

//echo '<script>console.log("techPublisher-top")</script>';

add_shortcode( 'technology_categories', 'inteum_category_grid' );

function inteum_category_grid() {
	ob_start();
?>
<div class="inteum_widget cl">
	<!--- Inteum: Category List 'Web Part' Grid with background images via jQuery -->
	<script id="scriptcategorylist" src="//utrf.technologypublisher.com/widget.aspx?type=cl&amp;num=12&amp;list=b&amp;space=0&amp;showcount=false&amp;cols=1&amp;colspace=0" type="text/javascript"></script>
</div>
<?php
	return ob_get_clean();
}
?>