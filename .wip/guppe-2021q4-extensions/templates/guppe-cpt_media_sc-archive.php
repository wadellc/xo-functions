<?php
/* 
 * Get Us PPE Media Center
 * Shortcode for Archive Media list.
 * Usage: [featured-news] 
 */



// Media Center: News Archive
function guppe_news_archive_shortcode() {
  ob_start();

  $args = array(
    'post_type'      => 'news',
    //'posts_per_page' => '2',
    'publish_status' => 'published',
    //'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    //'paged' => $paged,

    'orderby'   => 'meta_value_num',
    'meta_key'  => 'mcnewsdate_of_original_post',
    'order'     => 'DESC',
  );
 
    $news_archives_query = new WP_Query($args);
    if($news_archives_query->have_posts()) : ?>


<div id="news-list" style="position: absolute;top:-30px;"><!-- News Anchor --></div>


<form method="post" action="<?php the_permalink(); ?>#news-list">
  <select name="outlet_filter" id="selectservice" class="postform" onchange="submit();">

    <!-- Outlets -->
    <option value="">News Outlets</option>
    <?php
        $terms = get_terms('news-outlet');
            if ( $terms ) {
                foreach ( $terms as $term ) { 
    ?>
                  <option <?php if($term->slug == $_POST['news-outlet']){ echo 'selected="selected"';} ?> value="<?php echo esc_attr( $term->slug )?>"><?php echo esc_html( $term->name ) ?></option>
    <?php }
            }
    ?>
  </select>

  <!-- Spokespersons -->
  <select name="spokesperson_filter" id="selectservice" class="postform" onchange="submit();">
    <option value="">Spokespeople</option>
    <?php
        $terms = get_terms('spokesperson');
            if ( $terms ) {
                foreach ( $terms as $term ) { 
    ?>
                  <option <?php if($term->slug == $_POST['spokesperson']){ echo 'selected="selected"';} ?> value="<?php echo esc_attr( $term->slug )?>"><?php echo esc_html( $term->name ) ?></option>
    <?php }
            }
    ?>
  </select>

  <!-- Type -->
  <select name="type_filter" id="selectservice" class="postform" onchange="submit();">
    <option value="">News Type</option>
    <?php
        $terms = get_terms('media-type');
            if ( $terms ) {
                foreach ( $terms as $term ) { 
    ?>
                  <option <?php if($term->slug == $_POST['media-type']){ echo 'selected="selected"';} ?> value="<?php echo esc_attr( $term->slug )?>"><?php echo esc_html( $term->name ) ?></option>
    <?php }
            }
    ?>
  </select>

  <!-- Topics -->
  <select name="topic_filter" id="selectservice" class="postform" onchange="submit();">
    <option value="">News topics</option>
    <?php
        $terms = get_terms('news-topic');
            if ( $terms ) {
                foreach ( $terms as $term ) { 
    ?>
                  <option <?php if($term->slug == $_POST['news-topic']){ echo 'selected="selected"';} ?> value="<?php echo esc_attr( $term->slug )?>"><?php echo esc_html( $term->name ) ?></option>
    <?php }
            }
    ?>
  </select>
</form>





<div class="guppe-mc all-media">

<?php       while($news_archives_query->have_posts()) :
            $news_archives_query->the_post() ;

			// Taxonomies:
			// get_the_term_list( $id, $taxonomy, $before, $sep, $after )
      // <span class="filter-name">Topic:</span> <span class="news-topic"><span>Topic1</span> <span>Topic2</span> <span>Topic3</span></span>
			$news_outlet = strip_tags(get_the_term_list( get_the_ID(), 'news-outlet','<span class="news-outlet"><span class="term">','</span>, <span class="term">', '</span></span>'), '<span>');
			$news_type = strip_tags(get_the_term_list( get_the_ID(), 'media-type','<span class="media-type"><span class="term">','</span>, <span class="term">', '</span></span>'), '<span>');
      $news_contact = strip_tags(get_the_term_list( get_the_ID(), 'spokesperson','<span class="spokesperson"><span class="term">','</span>, <span class="term">', '</span></span>'), '<span>');
			$news_topic = strip_tags(get_the_term_list( get_the_ID(), 'news-topic','<span class="news-topic"><span class="term">','</span>, <span class="term">', '</span></span>'), '<span>');

      $news_outlet_link = get_the_term_list( get_the_ID(), 'news-outlet','<span class="news-outlet"><span class="term">','</span>, <span class="term">', '</span></span>');
      $news_type_link = get_the_term_list( get_the_ID(), 'media-type','<span class="media-type"><span class="term">','</span>, <span class="term">', '</span></span>');
      $news_contact_link = get_the_term_list( get_the_ID(), 'spokesperson','<span class="spokesperson"><span class="term">','</span>, <span class="term">', '</span></span>');
      $news_topic_link = get_the_term_list( get_the_ID(), 'news-topic','<span class="news-topic"><span class="term">','</span>, <span class="term">', '</span></span>');

      $origin_date = rwmb_meta( 'mcnewsdate_of_original_post' );

?>
    <div class="news-item">

      <div class="post-meta">
        <div class="date">
          <span class="month"><?php echo date( 'M', strtotime($origin_date) ); ?></span>
          <span class="day"><?php echo date( 'd', strtotime($origin_date) ); ?></span>
          <span class="year"><?php echo date( 'Y', strtotime($origin_date) ); ?></span>
        </div>
      </div>

      <div class="news-name">
        <h2 class="title"><a target="_blank" href="<?php echo rwmb_meta( 'mcnewslink_to_media_coverage' ) ?>"><?php echo get_the_title(); ?></a></h2>
      </div>

      <div class="small-date"><!-- Raw Date: <?php echo $origin_date ?> | (YYYY-MM-DD) | Tody's Date: <?php echo date('Y-m-d');?> --><?php echo date( 'F d, Y', strtotime($origin_date) ); ?></div>

<!--       <div class="filters row">
        <div class="column"><div><span class="filter-name">News Outlet:</span> <?php echo $news_outlet; ?></div></div>
        <div class="column"><div><span class="filter-name">Type:</span> <?php echo $news_type; ?></div></div>
        <div class="column"><div><span class="filter-name">Spokespeople:</span> <?php echo $news_contact; ?></div></div>  
        <div class="column"><div><span class="filter-name">Topics:</span> <?php echo $news_topic; ?></div></div>   
      </div> -->

      <div class="filters row links">
        <div class="column"><div><span class="filter-name">News Outlet:</span> <?php echo $news_outlet_link; ?></div></div>
        <div class="column"><div><span class="filter-name">Type:</span> <?php echo $news_type_link; ?></div></div>
        <div class="column"><div><span class="filter-name">Spokespeople:</span> <?php echo $news_contact_link; ?></div></div>  
        <div class="column"><div><span class="filter-name">Topics:</span> <?php echo $news_topic_link; ?></div></div>   
      </div>

    </div>

<?php       endwhile;
            //the_posts_pagination(); 
?>

</div><!-- /.guppe-mc.all-media -->
<div class="guppe-mc all-media pagi">
  <?php
  $big = 999999999; // need an unlikely integer
   
  echo paginate_links( array(
      'base' => str_replace( $big, '%#%#news-list', esc_url( get_pagenum_link( $big ) ) ),
      'format' => '?paged=%#%#news-list',
      'current' => max( 1, get_query_var('paged') ),
      'total' => $news_archives_query->max_num_pages
  ) );
  ?>
</div>

<?php
       wp_reset_postdata();
 
  endif; 
  return ob_get_clean();
}
add_shortcode('archive-news', 'guppe_news_archive_shortcode');
// News Archive shortcode code ends here



