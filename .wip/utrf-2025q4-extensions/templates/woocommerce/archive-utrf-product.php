<?php
/**
 * Custom Product Content Template for UTRF Product Categories
 * Developed for UTRF by David W. Couch 2/1/2017
 * Revisions 2021: Removal of Bootstrap dependancies for layout and T&C
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo '<script>console.log("woo-product-archive-top")</script>';

//echo "Shop Header";
get_header( 'shop' ); ?>
<!-- archive-utrf-product 2021 -->
<?php 
	//$product_ID = get_the_id();
	$my_product_cat = get_queried_object();
	$my_procat_ID = $my_product_cat->term_id;

    //get Pods object for current post
    $pod_catextend = pods( 'product_cat', $my_procat_ID );

	//get relationship fields post/page ID's; make links with permalink.
	$tandc_field = $pod_catextend->field( 'terms_conditions' );
/*	if ( $tandc_field ) {
		$tc_id = $tandc_field[ 'ID' ];
		echo '<a id="PageID_'.$tc_id.'" class="termslink" target=_blank" href="'.get_permalink( $tc_id ).'">'.get_the_title( $tc_id ).'</a><br>';
	}*/
		if ( $tandc_field ) {
            $tc_id = $tandc_field[ 'ID' ]; // NOT 'id'. ID is the Post ID of the CatT&C Page
            $tc_title = get_the_title( $tc_id );
            $tc_link = get_permalink( $tc_id );

            $tc_slug = get_term_by('id', $key, 'product_cat', 'ARRAY_A');
            $tc_slug = $tc_slug['slug'];
        }

	$fr_field = $pod_catextend->field( 'further_reading' );
/*	if ( $fr_field ) {
		$fr_id = $fr_field[ 'ID' ];
		echo '<a id="PageID_'.$fr_id.'" class="furtherlink" target=_blank" href="'.get_permalink( $fr_id ).'">'.get_the_title( $fr_id ).'</a><br>';
	}*/

	// Category Thumbnail - Full size
	$thumbnail_id = get_woocommerce_term_meta( $my_product_cat->term_id, 'thumbnail_id', true );
    $image = wp_get_attachment_url( $thumbnail_id );
/*    if ( $image ) {
	    echo '<img src="' . $image . '" alt="" />';
	}*/
?>
<!-- Page Template has: get_header(); then... -->
<!-- ::Content Header -->
<?php /*get_template_part( 'content', 'header' );*/ ?>
	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		//do_action( 'woocommerce_before_main_content' );
	?>

	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

		<!-- <h1 class="page-title"><?php woocommerce_page_title(); ?></h1> -->

		<?php /* <header> copied from content-headr.php */ ?>
		<header class="content-header">
            <!-- <div class="title-bar"> -->
                <div class="container">
                    <!-- <h1 class="page-title"><?php echo get_the_title( $post->post_parent ); /*echo $title; * displayed imgage title vs. page title */ ?></h1> -->
                    <?php /*if ( $subtitle ) printf( '<h3 class="page-subtitle taxonomy-description">%s</h3>', $subtitle );*/ ?>
                    <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
                </div>
            <!-- </div> -->
		</header>

	<?php endif; ?>



<div class="container">
<div id="main-grid" class="row">

<div id="primary" class="content-area-wide col-md-12">
<main id="main" class="site-main" role="main">
<article class="page type-page status-publish hentry no-wpautop">
	<div class="entry-content">
		<section class="row">
			<div class="col-md-6">
				<?php
				    if ( $image ) {
					    echo '<img src="' . $image . '" alt="" />';
					}
				?>
			</div>
			<div class="col-md-6 altquote vcenter">

					<?php
						/**
						 * woocommerce_archive_description hook.
						 *
						 * @hooked woocommerce_taxonomy_archive_description - 10
						 * @hooked woocommerce_product_archive_description - 10
						 */
						do_action( 'woocommerce_archive_description' );
					?>
			</div>
		</section>

		<section class="row">
			<?php /*echo do_shortcode('[woocommerce_cart]');*/ ?>
		</section>

<!-- <h2>Crystals Available for License</h2> -->
<?php
	if ( $fr_field ) {
		$fr_id = $fr_field[ 'ID' ];
		echo '<a id="PageID_'.$fr_id.'" class="furtherlink" target=_blank" title="'.get_the_title( $fr_id ).'" href="'.get_permalink( $fr_id ).'">More information about TennXC</a><br>';
	}else {
    	echo "<p>&nbsp;</p>";
	}
?>

<div class="col-sm-12 text-right">	
<section class="graypanel row text-left">
<div class="col-sm-12">
<h3>Requirements for Licensing</h3>
<strong>You are indicating your acceptance of the terms and conditions set forth by the UT Research Foundation.</strong>
<p>Please read and check the required boxes.</p>

<form>
<ul class="list-unstyled">
 	<li><label><input id="accept" type="checkbox" /> I have read and agree to the <?php if ( $tandc_field ) {
		$tc_id = $tandc_field[ 'ID' ];
		echo '<a id="PageID_'.$tc_id.'" class="termslink" data-toggle="modal" data-target="#myModal-'.$tc_slug.'">'.$tc_title.'</a>';
	}	?>.</label></li>
 	<li><label><input id="refund" type="checkbox" /> I understand that any and all license fees are non-refundable.</label></li>
 	<li><label><input id="rep" type="checkbox" /> I certify that I am an authorized representative of my institution.</label></li>
</ul>
</form></div>
</section>

<?php
if ( $tandc_field ) { ?>

                <!-- Modal -->
                <div class="modal fade" id="<?php echo 'myModal-'.$tc_slug.'"'?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo 'myModal-'.$tc_slug.'"'?>Label">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="<?php echo 'myModal-'.$tc_slug.'"'?>Label"><?php echo $tc_title; ?></h4>
                      </div>
                      <div class="modal-body">
                        <?php echo get_post_field('post_content', $tc_id); ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>

                        <?php
}// end if ?>




<hr>
				<a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
					<?php echo sprintf ( _n( '%d item', '%d items in cart', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?> - <?php echo WC()->cart->get_cart_total(); ?>
				</a>
<hr>
</div>

		<?php if ( have_posts() ) : ?>
			<?php echo "<!-- Before Shop - Search results and Ordering -->"; ?>
			<?php
				/**
				 * woocommerce_before_shop_loop hook.
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				/*do_action( 'woocommerce_before_shop_loop' );*/
			?>

			<div class="col-sm-12 text-right"></div>

			<?php /*echo "<!-- Product Loop - Start -->";*/ ?>
			<?php /*woocommerce_product_loop_start();*/ ?>

				<?php /*echo "<!-- Subcategories - 0 -->";*/ ?>
				<?php /*woocommerce_product_subcategories();*/ ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php echo "<!-- Product - Listing -->"; ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php /*woocommerce_product_loop_end();*/ ?>
			<?php /*echo "<!-- Product Loop - End -->";*/ ?>

			<?php echo "<!-- After Shop - Pagination -->"; ?>
			<?php
				/**
				 * woocommerce_after_shop_loop hook.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				/*do_action( 'woocommerce_after_shop_loop' );*/
			?>

<?php echo "<!-- Subcat Loop - 0 -->"; ?>
		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php /*echo "After Main";*/ ?>
	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		//do_action( 'woocommerce_after_main_content' );
	?>



	</div><!-- /.entry-content -->
</article>
</main><!-- #main -->
</div><!-- #primary -->


	<?php //get_sidebar(); ?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		//do_action( 'woocommerce_sidebar' );
	?>


</div><!-- .row -->
</div><!-- .container -->

<?php echo "<!-- Shop Footer - 0 -->"; ?>

<?php get_footer( 'shop' ); ?>
