<?php

/* Filter the single_template with our custom function*/
add_filter('single_template', 'my_custom_template');

function my_custom_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'rm_videos' ) {
        if ( file_exists( plugin_dir_path(__FILE__) . 'templates/rm_videos-single.php' ) ) {
            return plugin_dir_path(__FILE__) . 'templates/rm_videos-single.php';
			//return "Hello World!!!";
        }else{
            echo("Something went wrong...");
        }
    }

    return $single;

}