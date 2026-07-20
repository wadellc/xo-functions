<?php
/*
Plugin Name: XO Functions
Description: Custom Functions for Iowa Home Crafters website. Added Google Analytics 1/3/2020.
Author: David Couch of Wade, LLC
*/



// Added Google Analytics 01/03/2020
// ~DWC/WadeLLC
function ns_google_analytics() { 

  if( !is_user_logged_in() ){
  // Add Analytics if the user is NOT logged in.
  ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-155279770-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-155279770-1');
    </script>

  <?php

    } else {
      echo '<!-- User is logged in - Google Analytics (UA-155279770-1) is disabled -->';
    }

  }
  
add_action( 'wp_head', 'ns_google_analytics', 10 );

?>