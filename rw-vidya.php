<?php
   /*
   Plugin Name: RW-Vidya
   Plugin URI: https://rhinoweb.co.uk
   description: A purchase-to-view video CMS.
   Version: 1.0
   Author: Reece Mathieson
   Author URI: https://rhinoweb.co.uk
   License: GPL2
   */

require_once('rw_vidyas-init.php');

require_once('rw_vidyas-admin.php');

require_once('rw_vidyas-shortcodes.php');

require_once('rw_vidyas-webhook.php');

require_once('rw_vidyas-emails.php');

/*Install database and database functions*/

require_once('rw_vidyas-database.php');

register_activation_hook( __FILE__, 'rw_vidyas_db_install' );

/****************** */