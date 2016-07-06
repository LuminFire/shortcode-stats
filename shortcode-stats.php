<?php
/*
Plugin Name: Shortcode Stats
Plugin URI: #
Description: Count all the shortcodes!
Author: Cimbura.com (Nick Ciske)
Version: 1.0
Author URI: http://cimbura.com/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_menu', 'wporg_custom_admin_menu' );

function wporg_custom_admin_menu() {
    add_submenu_page(
    	'tools.php',
    	'Shortcode Stats',
        'Shortcode Stats',
        'manage_options',
        'shortcode-stats',
        'shortstats_admin_page'
    );
}

function shortstats_admin_page(){
	?>
    <div class="wrap">
        <h2>Shortcode Stats</h2>
        <?php shortstats_detect_and_count_shortcodes(); ?>
    </div>
    <?php

}

function shortstats_detect_and_count_shortcodes(){

    global $wpdb;

	$query_for_shortcodes = "SELECT post_content FROM " . $wpdb->posts . " WHERE post_status = 'publish' AND post_content LIKE '%[%]%'";

    $posts_with_shortcodes = $wpdb->get_results( $query_for_shortcodes, ARRAY_N );

	$pattern = '/\[(.*?)\]/';

    $shortcode_count = array();

    if ( $posts_with_shortcodes ) {

		foreach( $posts_with_shortcodes as $post ){

				$content = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", current( $post ) );
				preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

				foreach( $matches as $match ){

				if( count( $match ) ){

					foreach( $match as $tag ){

							$tag = trim( str_replace( array( '[', ']' ) , '', $tag ) );

							if( ! isset( $shortcode_count[ $tag ]  ) ){
								$shortcode_count[ $tag ] = 1;
							}else{
								$shortcode_count[ $tag ] = $shortcode_count[ $tag ] + 1;
							}

						}

					}

				}

		}

		arsort( $shortcode_count );

	    echo '<table class="widefat" style="width:30%"><thead>';
	    echo '<tr><th>Shortcode</th><th>Count</th></tr></thead><tbody>';

	    foreach( $shortcode_count as $key => $val ){
		    echo '<tr><td>[' . $key . ']</td><td>' . $val . '</td></tr>';
	    }

		echo '</tbody></table>';

	} else {
		// no posts found
		echo 'No posts found with shortcodes';
	}

}