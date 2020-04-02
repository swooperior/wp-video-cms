<?php
/*
rw_vidyas database helper functions.

Author: Reece Mathieson

*/


global $rw_vidyas_db_version;
//Update version when database schema is changed.
$rw_vidyas_db_version = 1.0;

//Create the database table
function rw_vidyas_db_install () {
    global $wpdb;
 
    $table_name = $wpdb->prefix . "rw_vidyas_purchases"; 

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    vidya_id mediumint(9) NOT NULL,
    customer_id mediumint(9) NOT NULL,
    s_payment_intent varchar(55) DEFAULT '' NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option( 'rw_vidyas_db_version', $rw_vidyas_db_version );

 }

 

 //Insert a new purchase into the database
 function rw_vidyas_db_insert($customer_id,$vidya_id,$s_payment_intent){
    global $wpdb;
    $table_name = $wpdb->prefix . "rw_vidyas_purchases"; 

    $wpdb->insert( 
        $table_name, 
        array( 
            'date' => current_time( 'mysql' ), 
            'vidya_id' => $vidya_id, 
            'customer_id' => $customer_id, 
            's_payment_intent' => $s_payment_intent
        ) 
    );
 }


 function list_vidya_purchases() {
    global $wpdb;
    $table_name = $wpdb->prefix . "rw_vidyas_purchases"; 
    require_once('vendor/autoload.php');
    $stripeSK = get_option('rwvidyas_stripe_sk');
    \Stripe\Stripe::setApiKey($stripeSK);

    $purchases = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
    $output = "<table id='rw_vidya_purchases'><tr><th>Customer</th><th>Video</th><th>Price</th><th>Status</th><th>Action</th></tr>";

    foreach($purchases as $purchase){
        $customer = get_userdata($purchase->customer_id);
        $video = $purchase->vidya_id;

        $cus_name = $customer->first_name." ".$customer->last_name;
        $vid_title = get_the_title($video);
        $vid_price = get_post_meta($video,'price_meta',true);
        $paymentIntent_obj = \Stripe\PaymentIntent::retrieve($purchase->s_payment_intent);
        $pay_status = $paymentIntent_obj['status'];
        $refund_status = $paymentIntent_obj['charges']['data'][0]['refunded'];
        if($refund_status != 1){
            //action_button needs to a button to refund if refunded === false
            $action_button = "Refund";
            $pay_status = "Completed";
        }else{
            $pay_status = "Refunded";
            $action_button = "";
        }

        $output .= "<tr><td>$cus_name</td><td>$vid_title</td><td>$vid_price</td><td>$pay_status</td><td>$action_button</td></tr>";
    }
    $output .= "</table>";
    return($output);
 }
