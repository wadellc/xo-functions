<?php
/*
Plugin Name: Lakemoor Site Extensions
Description: Code added to improve user experience or performance of lakemoor.org
Author: David Couch
*/



// Redirect private bbPress forum to specific page.
//https://easywebdesigntutorials.com/hide-buddypress-pages-and-bbpress-forums-from-not-logged-in-users/
function private_content_redirect_to_login() {
    global $wp_query,$wpdb;
    if (is_404() and !is_user_logged_in()) {
      $host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      $path = dirname($_SERVER['REQUEST_URI']);
      $forums = "new-forums";
      $location = "https://lakemoor.org/login/";
      }
      if(strpos( $path, $forums ) !== false){
        wp_safe_redirect($location);
        exit;
    }
}
//add_action('template_redirect', 'private_content_redirect_to_login', 9);


// Redirect Reply Posts to Topic With Reply Anchor
// https://gist.github.com/jrevillini/7f38ee887d7e6919cfc31c6a7e2cc514
function jrevillini_reply_redirect( $wp_query ) {
  if ( !function_exists('bbp_get_reply_url') ) return; // skip this if bbpress not active
  if ( !isset($wp_query->query['reply']) ) return; // skip if not a bbpress reply page
  if ( isset($wp_query->query['edit']) ) return; //skip if we are EDITING a bbpress reply
  wp_safe_redirect( bbp_get_reply_url( $wp_query->query['reply'] ) );  
}
//add_action('pre_get_posts', 'jrevillini_reply_redirect');
  
  

?>