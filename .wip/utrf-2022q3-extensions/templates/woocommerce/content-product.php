<?php
/**
 * The template for displaying product content within loops
 *
 * Custom Product Content Template for UTRF TennXC Crystals
 * Developed for UTRF by David W. Couch 2/1/2017
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
echo "<!-- Custom Product Content Template for UTRF  -->";


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<article class="img-left row product-content utrf">
	    <div class="col-md-7">
	        <?php the_post_thumbnail('thumbnail', array('class' => 'alignleft size-thumbnail ')); ?>
	        <?php the_title( '<h4>', '</h4>' ); ?>
	        <?php the_content(); ?>
	        <p>Template via plugin</p>
	    </div>
	    <div class="col-md-1">&nbsp;</div>
        <div class="col-md-4">
        <?php
            /**
             * woocommerce_single_product_summary hook.
             *
             * @hooked woocommerce_template_single_title - 5
             * @hooked woocommerce_template_single_rating - 10
             * @hooked woocommerce_template_single_price - 10
             * @hooked woocommerce_template_single_excerpt - 20
             * @hooked woocommerce_template_single_add_to_cart - 30
             * @hooked woocommerce_template_single_meta - 40
             * @hooked woocommerce_template_single_sharing - 50
             */
            
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
            do_action( 'woocommerce_single_product_summary' );
        ?>
    </div>
</article>
<!-- <li <?php post_class(); ?>> -->
	<?php echo "<!-- Before Shop Loop Item -->"; ?>
	<?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	//do_action( 'woocommerce_before_shop_loop_item' );


	echo "<!-- Before Shop Loop Item Title-->";
	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	//do_action( 'woocommerce_before_shop_loop_item_title' );

/*<img width="150" height="150" src="https://66nw33kfguicy9f09yk8b54a-wpengine.netdna-ssl.com/wp-content/uploads/NbS2-1-150x150.jpg" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="NbS2" title="NbS2" srcset="https://66nw33kfguicy9f09yk8b54a-wpengine.netdna-ssl.com/wp-content/uploads/NbS2-1-150x150.jpg 150w, https://66nw33kfguicy9f09yk8b54a-wpengine.netdna-ssl.com/wp-content/uploads/NbS2-1-75x75.jpg 75w" sizes="(max-width: 150px) 100vw, 150px">*/


	echo "<!-- Shop Loop Item Title -->";
	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	//do_action( 'woocommerce_shop_loop_item_title' );

/*<h3>NbS2 Niobium Disulfide Crystal</h3>*/

	echo "<!-- After Shop Loop Item Title -->";
	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	//do_action( 'woocommerce_after_shop_loop_item_title' );

//the_excerpt();

/*<span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>350.00</span></span>*/


	echo "<!-- After Shop Loop Item -->";
	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	//do_action( 'woocommerce_after_shop_loop_item' );
	?>



<!-- <a rel="nofollow" href="https://utrf.tennessee.edu/product/nbs2-niobium-disulfide-crystal/" data-quantity="1" data-product_id="5923" data-product_sku="" class="button product_type_variable add_to_cart_button">Select options</a> -->


	
<!-- </li> -->

<hr>
