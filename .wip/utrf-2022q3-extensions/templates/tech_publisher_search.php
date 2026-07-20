<?php
/* 
 * UTRF Technology Publisher Search
 * Usage: [technology_search]
 */

add_shortcode( 'technology_search', 'inteum_technology_search' );

function inteum_technology_search() {
	ob_start();
?>
<div class="inteum_widget tsb">
	<!--- Inteum Search Box 'Web Part' 🔎︎ -->
	<script id="scripttechnologysearchbox" src="//utrf.technologypublisher.com/widget.aspx?type=tsb&width=200&btn=Search" type="text/javascript"></script>
</div>
<?php
	return ob_get_clean();
}
?>