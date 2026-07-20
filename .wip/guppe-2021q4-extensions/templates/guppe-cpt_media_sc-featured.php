<?php
/* 
 * Get Us PPE Media Center
 * Shortcode for Featured Media content slider.
 * Usage: [featured-news] 
 */



// Media Center: Featured Media
function guppe_featured_news_shortcode(){
    ob_start();
    $args = array(
                    'post_type'      => 'news',
                    'posts_per_page' => '5',
                    'publish_status' => 'published',
                    'meta_query' => array(array('key' => '_thumbnail_id')),
                    'facetwp'	=> false,
                 );
 
    $feat_media_query = new WP_Query($args); 
    if($feat_media_query->have_posts()) : ?>

<div class="guppe-mc featured-media">
<!-- Swiper | Added 'swiper' class 11/09/2021 ~DWC-->
<div class="swiper swiper-container">

	<!-- Add Pagination -->
	<div class="swiper-pagination"></div>

	<!-- Add Arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>


	<div class="swiper-wrapper">



<?php   while($feat_media_query->have_posts()) : 
            $feat_media_query->the_post() ;

			// Taxonomies:
			// get_the_term_list( $id, $taxonomy, $before, $sep, $after )
			$news_outlet = get_the_term_list( get_the_ID(), 'news-outlet','<span class="news-outlet">','&npsp,', '</span>');
			$news_contact = get_the_term_list( get_the_ID(), 'spokesperson','<span class="spokesperson">','&npsp,', '</span>');
			$news_type = get_the_term_list( get_the_ID(), 'media-type','<span class="media-type">','&npsp,', '</span>');
			$news_topic = get_the_term_list( get_the_ID(), 'news-topic','<span class="news-topic">','&npsp,', '</span>');

			$trimmed_content = wp_trim_words( get_the_excerpt(), $num_words = 35, $more = null );
			$origin_date = rwmb_meta( 'mcnewsdate_of_original_post' );
?>

    			<div class="swiper-slide">
			        <div class="news-item">
			        	<div class="news-poster">
			        		<?php echo get_the_post_thumbnail(); ?>
			        	</div>
			        	<span class="photo-credit"><!-- Photo Credit:  --><a target="_blank" href="<?php echo rwmb_meta( 'mcnewsphoto_credit_link' ); ?>"><?php echo rwmb_meta( 'mcnewsphoto_credit' ); ?></a></span>
			        	
			        	<div class="news-name"><h2 class="title"><a target="_blank" href="<?php echo rwmb_meta( 'mcnewslink_to_media_coverage' ); ?>"><?php echo get_the_title(); ?></a></h2></div>
			        	<span class="news-outlet"><?php echo $news_outlet; ?></span><span class="news-date"> | <?php echo date( 'F d, Y', strtotime($origin_date) ); ?> </span>
				        <div class="excerpt">
				        	<p><?php echo $trimmed_content; ?> <span><a target="_blank" href="<?php echo rwmb_meta( 'mcnewslink_to_media_coverage' ); ?>">View</a></span></p>
				        </div> 
					<!-- 			    
						<div>Type: <?php echo $news_type; ?></div>
				        <div>Topic: <?php echo $news_topic; ?></div>
				        <div>Spokespeople: <?php echo $news_contact; ?></div>
				    -->
			        </div><!-- / .news-item -->
			    </div>
<?php
        endwhile;
?>

	</div><!-- / .swiper-wrapper -->
</div><!-- / .swiper-container -->
</div><!-- / .guppe-mc.featured-media -->









  <!-- Swiper JS -->
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
	jQuery(function() {
	    
	    var swiper = new Swiper('.swiper-container', {
	      slidesPerView: 1,
	      spaceBetween: 30,
	      pagination: {
	        el: '.swiper-pagination',
	        clickable: true,
	      },
	      navigation: {
	        nextEl: '.swiper-button-next',
	        prevEl: '.swiper-button-prev',
	      },
	    });

/*    	var imgHeight = jQuery(".swiper-slide-active img").height();
    	var swiprnav = jQuery(".swiper-pagination").height();
    	var newPosition = imgHeight-(2*swiprnav);
		jQuery("div.swiper-container-horizontal>.swiper-pagination-bullets").css({top: newPosition });
		jQuery("div.swiper-button-next").css({top: imgHeight-swiprnav });
		jQuery("div.swiper-button-prev").css({top: imgHeight-swiprnav });*/

	});
  </script>


  <?php


        wp_reset_postdata();
 
    endif;    
    //return $result;
    return ob_get_clean();
}
 
add_shortcode( 'featured-news', 'guppe_featured_news_shortcode' ); 
// Featured News shortcode code ends here