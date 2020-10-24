<?php
   /*
   Plugin Name: RM-Videos
   Plugin URI: https://reecemathieson.dev 
   description: A purchase-to-view video CMS with Vimeo.
   Version: 0.1
   Author: Reece Mathieson
   Author URI: https://reecemathieson.dev
   License: GPL2
   */

require_once('rm_videos-init.php');

require_once('rm_videos-admin.php');

require_once('rm_videos-shortcodes.php');

require_once('rm_videos-webhook.php');

require_once('rm_videos-emails.php');

require_once('rm_videos_default_post.php');

/*Install database and database functions*/

require_once('rm_videos-database.php');

register_activation_hook( __FILE__, 'rm_videos_db_install' );


/****************** */