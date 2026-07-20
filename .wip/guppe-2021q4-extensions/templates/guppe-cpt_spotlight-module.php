<?php
/* 
 * Get Us PPE Spotlight Module - From the Frontlines
 * Shortcode for Spotlight Module content slider.
 * Usage: [guppe-spotlight] 
 */

// Spotlight Module Shortcode
add_shortcode( 'guppe-spotlight', 'guppe_spotlight_shortcode' ); 
function guppe_spotlight_shortcode( $atts ){
    ob_start();

    $random_num6 = random_int(100000, 999999);

    // define attributes and their defaults
    $attributes = shortcode_atts( 
        [
            'posts'    => -1,
            'order'    => 'DESC',
            'orderby'  => 'date', 
            'category' => 'all',// If cat is '' and none passed - no posts display
            'section' => '',
        ],
        $atts 
    );

    // variables from attributes
    $posts      = $attributes['posts'];
    $order      = $attributes['order'];
    $orderby    = $attributes['orderby'];
    $category   = $attributes['category'];
    $section    = $attributes['section'];

    $tax_query = array(
        'taxonomy' => 'spotlight-category',
        'field'    => 'slug',
        'terms'    => array( $category ), // <--------
        'operator' => 'IN',
    );
 
    // define query parameters based on attributes - overidding defaults
    $options = array(
        'post_type' => 'guppe_spotlight',
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
        'section' => $random_num6, // 6 digit random number
        'publish_status' => 'published',
        'facetwp'   => false,

        // No Impact
        // 'terms' => $category,

        // Only allows single cat. Does not work with comma seperated list.
        'tax_query'   => array($tax_query),

    );

    // print_r($options);
    // var_dump($atts);


    $guppe_spotlights = new WP_Query($options); 
    if($guppe_spotlights->have_posts()) : 
?>

<div id="spotlight-wrapper-<?php echo $random_num6; ?>" class="guppe-slm spotlight-wrapper">>
<div id="spotlight-toggle-<?php echo $random_num6; ?>" class="slm-toggle"><span>Stories from the Front Lines</span></div>
<div id="spotlight-content-<?php echo $random_num6; ?>" class="guppe-slm spotlight-container">
	

    <!-- Swiper -->
    <div class="swiper-container">
      <div class="swiper-wrapper">
<?php   
		while ($guppe_spotlights->have_posts()) : 
               $guppe_spotlights->the_post() ;

    			// Spotlight Field ID's
    			// $slm_container
    			$guppe_slm_quote = rwmb_meta( 'guppe_slm_quote' );
    			$guppe_slm_name = rwmb_meta( 'guppe_slm_name' );
    			$guppe_slm_organization = rwmb_meta( 'guppe_slm_organization' );
    			$guppe_slm_city = rwmb_meta( 'guppe_slm_city' );
    			$guppe_slm_state = rwmb_meta( 'guppe_slm_state' );

    			$guppe_slm_post = rwmb_meta( 'guppe_slm_post' ); // Post ID
    			 $guppe_author_id = get_post_field ('post_author', $guppe_slm_post); //Author ID
    			 $guppe_author_name = get_the_author_meta( 'user_firstname' , $guppe_author_id )  . ' ' . get_the_author_meta( 'user_lastname' , $guppe_author_id );

                $guppe_slm_working_title  = rwmb_meta( 'guppe_slm_working_title' ); 
                $guppe_slm_url  = rwmb_meta( 'guppe_slm_url' ); 

?>
        <div class="swiper-slide">
        	<div class="spotlight-item">
                <img class="q-mark" src="<?php echo plugin_dir_url( __FILE__ ) . '../img/quotation-mark.png'; ?>">
	        	<div class="spotlight-quote">
					<?php echo $guppe_slm_quote; ?>
				</div>

                <div class="spotlight-attribution">
                    <?php
                    if(!empty($guppe_slm_name)){
                        $guppe_slm_name = '— '.$guppe_slm_name; // &mdash;
                     }
                    // Attribution 
                    $vars = array_filter(array($guppe_slm_name, $guppe_slm_organization, $guppe_slm_city, $guppe_slm_state));
                    echo implode(', ', $vars);
                    ?>
                </div>

	        	<?php edit_post_link('Edit this Spotlight','',' '); ?>

        	</div>

            <?php /*echo $guppe_slm_post;*/ // NO LINK IF NO LINK 

            // If PostID 
            //  Title = Post Title
            //  Link = Post Link
            if($guppe_slm_post){
                $title = esc_html( get_the_title($guppe_slm_post) ); 
                $link = esc_html( get_permalink($guppe_slm_post) );
            }

            // If Working Title 
            //    Title = Working Title
            if ($guppe_slm_working_title){
                $title = $guppe_slm_working_title;
            }

            // If URL Link = URL
            if ($guppe_slm_url){ 
                $link = $guppe_slm_url;
                $target = '_blank';
            } 

            ?>

			<div class="spotlight-source">
                <div class="source-wrap">
				<?php 
                if ($link) {
                    echo '<a class="guppe-link" target="'.$target.'" href="' . $link . '">' . $title . '</a>';
                    $link = '';
                    $title = '';
                    $target = '';
                }  else {
                    echo '-';
                } ?>
               </div><span class="chev">&raquo;</span>
			</div>
            
        </div>

<?php 	
		endwhile;	
?>

      </div><!-- /.swiper-wrapper -->
      <!-- Add Pagination -->
      <div id="<?php echo $section; ?>-paging" class="swiper-pagination"></div>
      <!-- Add Arrows -->
      <div id="<?php echo $section; ?>-next" class="swiper-button-next"></div>
      <div id="<?php echo $section; ?>-prev" class="swiper-button-prev"></div>
    </div>


</div><!-- / .guppe-slm.spotlight-container -->
</div><!-- / #spotlight-wrapper -->



<!-- Swiper JS -->
<!-- <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> -->


    <!-- Initialize Swiper -->
    <script>
    jQuery(document).ready(function() {
          var swiper = new Swiper(".swiper-container", {
            slidesPerView: 1,
            centeredSlides: true,
            spaceBetween: 30,
            grabCursor: true,
            loop: true,
            loopFillGroupWithBlank: false,
            breakpointsBase: window,
            breakpoints: {
                // when window width is >= 660px
                660: {
                  slidesPerView: 1,
                },
                // when window width is >= 660px
                800: {
                  slidesPerView: 2,
                },
                // when window width is >= 1200px
                1200: {
                  slidesPerView: 3,
                },
            },

            pagination: {
              el: "#<?php echo $section; ?>-paging",
              clickable: true
            },

            navigation: {
              nextEl: "#<?php echo $section; ?>-next",
              prevEl: "#<?php echo $section; ?>-prev"
            }
          });
    });
    jQuery(document).ready(function() {
        jQuery( "#spotlight-toggle-<?php echo $random_num6; ?>" ).click(function() {
            // 2000,"linear",  vs "slow"
          jQuery( "#spotlight-content-<?php echo $random_num6; ?>" ).slideToggle( 'fast', function() {
            // Animation complete.
            jQuery(this).toggleClass("open");
            jQuery("#spotlight-toggle-<?php echo $random_num6; ?>").toggleClass("closed");
          });
        });

    });

    // On load close 'em.'
    jQuery(document).ready(function() {
        // jQuery( "#spotlight-toggle-<?php echo $random_num6; ?>" ).click(function() {
            // 2000,"linear",  vs "slow"
          jQuery( "#spotlight-content-<?php echo $random_num6; ?>" ).slideToggle( 'fast', function() {
            // Animation complete.
            jQuery(this).toggleClass("open");
            jQuery("#spotlight-toggle-<?php echo $random_num6; ?>").toggleClass("closed");
          });
        /*});*/

    });
    </script>


  <?php
	wp_reset_postdata();
    endif;    
    //return $result;
    return ob_get_clean();
} // Spotlight Module shortcode ends here

