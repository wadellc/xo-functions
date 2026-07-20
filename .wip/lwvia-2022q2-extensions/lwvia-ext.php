<?php
/*
 * Plugin Name:       LWVIA Extensions
 * Plugin URI:        
 * Description:       Support specific to LWVIA
 * Version:           1.0.0
 * Author:            Wade, LLC
 * Author URI:        https://wadellc.co
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lwvia-ext
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/************* INCLUDE NEEDED FILES ***************/

/*
1. /lwvia-extensions/lwvia-ext.php
    - enqueueing scripts & styles
*/

/* 
 * Enqueue styles and supporting scripts for LWVIA custom post types 
 * //wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
 * //wp_enqueue_style( $handle, $src, $deps, $ver, $media );
 */
add_action( 'wp_enqueue_scripts', 'lwvia_styles_and_scripts' );
function lwvia_styles_and_scripts() {

    $plugin_url = plugin_dir_url( __FILE__ );

    
// LWVIA Scripts, Fonts, Styles
    
    //wp_enqueue_script( 'lwvia_script', $plugin_url . 'js/lwvia.js', array( 'jquery' ), '2.0', true );
    wp_enqueue_style( 'lwvia_style', $plugin_url . 'css/lwvia.css' );

    //wp_enqueue_style( 'add_google_fonts', 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400&family=Merriweather:wght@400;700;900&display=swap', false );
	
	// Font Revised 06/04/2022 - Enqueue did not work - See @import in CSS above
	//wp_enqueue_style( 'add_google_fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Serif&family=Inter', false );
	

}

/*
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Serif&family=Inter&display=swap" rel="stylesheet">

font-family: 'Bebas Neue', cursive;
font-family: 'IBM Plex Serif', serif;
font-family: 'Inter', sans-serif;

*/


/* Existing GTM + FB tracking snippets 

<script>
(function(w,d,s,l,i){
    w[l]=w[l]||[];
    w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
    var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
    j.async=true;
    j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
    f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M9FZBBP');
</script>


<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1550821871601980');
    fbq('track', 'PageView');
</script>

<noscript>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=1550821871601980&ev=PageView&noscript=1"/>
</noscript>

*/



/* Not working... */
//add_action('wp_body_open', 'lwvia_noscript_gtm');
function lwvia_noscript_gtm() { 
    echo '<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T6JN3M7"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->';
}

/* Auto Copyright Date 
 * Usage: <span id="current-year"></span>
 */

add_action('wp_footer', 'lwvia_current_year');
function lwvia_current_year() { ?>
    <script>
        document.getElementById("current-year").innerHTML = new Date().getFullYear();
    </script>
<?php
}
