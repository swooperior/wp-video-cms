<?php   // Register Custom Post Type
function create_rw_vidyas() {

	$labels = array(
		'name'                  => _x( 'Videos', 'Post Type General Name', 'rw_vidyas' ),
		'singular_name'         => _x( 'Video', 'Post Type Singular Name', 'rw_vidyas' ),
		'menu_name'             => __( 'Videos', 'rw_vidyas' ),
		'name_admin_bar'        => __( 'Videos', 'rw_vidyas' ),
		'archives'              => __( 'Video Archives', 'rw_vidyas' ),
		'attributes'            => __( 'Video Attributes', 'rw_vidyas' ),
		'parent_item_colon'     => __( 'Parent Video:', 'rw_vidyas' ),
		'all_items'             => __( 'All Videos', 'rw_vidyas' ),
		'add_new_item'          => __( 'Add New Video', 'rw_vidyas' ),
		'add_new'               => __( 'Add New', 'rw_vidyas' ),
		'new_item'              => __( 'New Video', 'rw_vidyas' ),
		'edit_item'             => __( 'Edit Video', 'rw_vidyas' ),
		'update_item'           => __( 'Update Video', 'rw_vidyas' ),
		'view_item'             => __( 'View Video', 'rw_vidyas' ),
		'view_items'            => __( 'View Videos', 'rw_vidyas' ),
		'search_items'          => __( 'Search Video', 'rw_vidyas' ),
		'not_found'             => __( 'Not found', 'rw_vidyas' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'rw_vidyas' ),
		'featured_image'        => __( 'Featured Image', 'rw_vidyas' ),
		'set_featured_image'    => __( 'Set featured image', 'rw_vidyas' ),
		'remove_featured_image' => __( 'Remove featured image', 'rw_vidyas' ),
		'use_featured_image'    => __( 'Use as featured image', 'rw_vidyas' ),
		'insert_into_item'      => __( 'Insert into Video', 'rw_vidyas' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Video', 'rw_vidyas' ),
		'items_list'            => __( 'Video list', 'rw_vidyas' ),
		'items_list_navigation' => __( 'Video list navigation', 'rw_vidyas' ),
		'filter_items_list'     => __( 'Filter Video list', 'rw_vidyas' ),
	);
	$capabilities = array(
		'edit_post'             => 'edit_video',
		'read_post'             => 'read_video',
		'delete_post'           => 'delete_video',
		'edit_posts'            => 'edit_videos',
		'edit_others_posts'     => 'edit_others_videos',
		'publish_posts'         => 'publish_videos',
		'read_private_posts'    => 'read_private_videos',
	);
	$args = array(
		'label'                 => __( 'Video', 'rw_vidyas' ),
		'description'           => __( 'Purchasable video lessons', 'rw_vidyas' ),
		'labels'                => $labels,
		'supports'              => array( 'title','thumbnail', 'excerpt', 'comments'),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-video-alt3',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capabilities'          => $capabilities,
		'show_in_rest'          => true,
	);
	register_post_type( 'rw_vidyas', $args );

    //Add video capabilities to administrator role.
    //Change - instead create video-author role and add this role to the admin user?
    //Would need to copy role capbabiltiies from elsewhere?
    $role = get_role( 'administrator' );
    foreach($capabilities as $cap){
        $role->add_cap( $cap, true );
	}
    
}
add_action( 'init', 'create_rw_vidyas', 0 );

//Register plugin stylesheet
function add_my_plugin_stylesheet(){
	wp_register_style('rw_vidyas-style','/wp-content/plugins/RW-Vidya/rw_vidyas-style.css');
	wp_enqueue_style('rw_vidyas-style');
}
add_action("wp_print_styles","add_my_plugin_stylesheet");

//Add custom fields to custom post type
add_action("admin_init", "init_videos");

function init_videos(){
  add_meta_box("video_details-meta", "Video Options", "rw_vidya_video_details", "rw_vidyas", "normal", "high");
}

function rw_vidya_video_details(){
  global $post;
  $custom = get_post_custom($post->ID);
  $vimeo_url = $custom["vimeo_url"][0];
  $price_meta = $custom["price_meta"][0];
	
  ?>
  
	<table>
		<tr><td><?php //VIMEO API REQUIRED require('rw_vidyas-vimeo.php'); ?> <tr></td>
		<tr><td><label>Vimeo ID: <p><i>After uploading your video to vimeo, copy the vimeo id (The string of numbers at the end of the video url) here.</i></p></label></td><td><input name="vimeo_url"  value="<?php echo $vimeo_url; ?>" /></td></tr>
		<tr><td><label>Price (&pound;):</label></td><td><input name="price_meta" size=10  value="<?php echo $price_meta; ?>" /></td></tr>
</table>
	
<input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(plugin_basename(__FILE__).$post->ID); ?>" />
  <?php

}

add_action('save_post', 'save_details');

function save_details(){
	global $post;
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], plugin_basename(__FILE__).$post->ID)){
		return $post_id;
	}

	update_post_meta($post->ID, "vimeo_url", $_POST['vimeo_url']);
	update_post_meta($post->ID, "price_meta", $_POST["price_meta"]);

}