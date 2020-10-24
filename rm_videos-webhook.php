<?php
add_action( 'rest_api_init', function () {
    register_rest_route( 'rm_videos/v1', '/endpoint', array(
      'methods'  => 'POST',
      'callback' => 'handle_vidya_payment',
    ) );
  } );

function handle_vidya_payment(){
    require_once('vendor/autoload.php');

    $stripeSK = get_option('rwvidyas_stripe_sk');
    \Stripe\Stripe::setApiKey($stripeSK);

    $payload = @file_get_contents('php://input');
    $event = null;

    try {
        $event = \Stripe\Event::constructFrom(
            json_decode($payload, true)
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
    }

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object; 
            //Get video and user information from stripe payment
            $customer = $paymentIntent['metadata']['customer'];
            $rm_video = $paymentIntent['metadata']['rm_video'];
            $rm_video_id = str_replace('rm_video_','',$rm_video);
            $s_payment_intent = $paymentIntent['id'];

            //Assign video to user
            $user = new WP_User($customer);
            $user->add_cap($rm_video,true);

            //Send the user a receipt.
            $email_to = $user->user_email;
            $email_subject = "Receipt from ".get_site_url();
            $email_body = rm_video_receipt_email($customer,$rm_video);
            $email_headers[] = 'Content-Type: text/html; charset=UTF-8';
            $email_headers[] = 'From: '.get_bloginfo('name').' <'.get_option('admin_email').'>'; //Currently from administrators email address.
            wp_mail($email_to,$email_subject,$email_body,$email_headers);

            //Add payment to database
            rm_videos_db_insert($customer,$rm_video_id,$s_payment_intent);

            break;
        case 'payment_intent.payment_failed':

            break;
        case 'charge.refunded':
            $paymentIntent = $event->data->object; 
            //Get video and user information from stripe payment
            $customer = $paymentIntent['metadata']['customer'];
            $rm_video = $paymentIntent['metadata']['rm_video'];
            $s_payment_intent = $paymentIntent['id'];
            
            //Remove video from user
            $user = new WP_User($customer);
            $user->remove_cap($rm_video,true);

            //Send the user a refund confirmation
            $email_to = $user->user_email;
            $email_subject = "Refund from ".get_site_url();
            $email_body = rm_video_refund_email($customer,$rm_video);
            $email_headers[] = 'Content-Type: text/html; charset=UTF-8';
            $email_headers[] = 'From: '.get_bloginfo('name').' <'.get_option('admin_email').'>'; //Currently from administrators email address.
            wp_mail($email_to,$email_subject,$email_body,$email_headers);

            break;
        default:
            // Unexpected event type
            http_response_code(400);
            exit();
    }


http_response_code(200);
}
