<?php
/* 
 * UTRF Staff Page Shortcode
 * Usage: [utrf_staff] 
 */

add_shortcode( 'utrf_staff', 'staff_bios_by_tag' );

function staff_bios_by_tag() {
	ob_start();
?>

		<main id="main" class="site-main utrf-staff" role="main">
            
            <?php 
            	// Tags added to Bios CPT
                $tag_row = array("leadership", "licensing", "legal", "marketing", "accounting-compliance"); 

                foreach ($tag_row as $tag) { 
                    $args = array(
                                'post_status' => 'publish' ,
                                'posts_per_page' => -1,
                                'post_type' => 'bio',
                                'orderby' => 'menu_order', 
                                'order' => 'ASC',
                                'tag' => $tag                
                            );
                    $query = new WP_Query($args);
                    ?>
						<!-- Begin Staff Group -->
                        <h2 class="staff_type"><?php echo $tag ?></h2>
                        <div class="row graypanel <?php echo $tag ?>">          
                            <?php while ( $query->have_posts() ) : $query->the_post(); global $post;?>
                                <?php 
                                /* Pods fields - set to variables */
                                    $job_title = get_post_meta($post->ID, 'job_title', true);
                                    $job_scope = get_post_meta($post->ID, 'job_scope', true);
                                    $phone = get_post_meta($post->ID, 'phone', true);
                                    $e_mail = get_post_meta($post->ID, 'e_mail', true);
                                ?>
				                <!-- Begin Bio  col-md-3-->

                                <div class="bio<?php print (' '.str_replace(' ', '-', strtolower($job_title))) ? : ''; ?>">
                                    <div class="media bio-pic">
										<a title="<?php echo the_title(); ?>" alt="<?php echo the_title(); ?>" href="<?php echo get_permalink(); ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail();} ?></a>
                                    </div>

                                    <div class="staff-name"><a href="<?php echo get_permalink(); ?>"><?php echo the_title(); ?></a></div>
                                    <?php /*echo the_excerpt();*/ ?>
                                    <?php /*echo the_meta();*/ ?>
                                    <div class="job-title"><?php echo $job_title; ?></div>             
                                    <div class="job-scope"><?php echo $job_scope; ?></div>
                                    <div class="phone"><a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></div>
                                    <div class="e-mail"><a href="mailto:<?php echo $e_mail; ?>"><?php echo $e_mail; ?></a></div>
                                </div><!-- End Bio -->

                            <?php endwhile; ?>
                        </div><!-- /<?php echo $tag ?> -->
						<!-- End Staff Group -->
            
            <?php 


				} // foreach
            ?>            

		</main><!-- #main -->

<?php
	return ob_get_clean();
} ?>