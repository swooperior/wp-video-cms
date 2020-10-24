<?php
function rm_videos_load_scripts() {
 
	wp_register_script( 'stripe-elements','https://js.stripe.com/v3/');
	wp_register_script('stripe-client', plugins_url('client.js', __FILE__), array('jquery'),'1.1', true);
	
	wp_enqueue_script('stripe-elements');
	wp_enqueue_script('stripe-client');
}
add_action( 'wp_enqueue_scripts', 'rm_videos_load_scripts' );  


add_shortcode("rm_video","rm_videos_show_video");

function rm_videos_show_video(){
	$customer = get_current_user_id();
	$customer_data = get_userdata($customer);
	$customer_email = $customer_data->user_email;
    $desc = get_the_excerpt();
    $price = get_post_meta(get_the_ID(),"price_meta", true);
	$vimeoID = get_post_meta(get_the_ID(),"vimeo_url",true);
	$vidya_perm = "rm_video_".get_the_ID();
    //Add some kind of check to see if the current logged in user has permissions to view the current video, if not give them the option to purchase it.
    if (current_user_can($vidya_perm) OR current_user_can('administrator')) {
		$output .="<div class='rm_videos_video'>
		 <iframe src='https://player.vimeo.com/video/".$vimeoID."' width='640' height='360' frameborder='0' allow='autoplay; fullscreen' allowfullscreen></iframe>
		</div>";
	}else{
		if(is_user_logged_in()){
			require_once('vendor/autoload.php');
			
			$newval = strval($price);
			$newval = str_replace(".","",$newval);
			$newval = intval($newval);
			$stripePK = get_option('rwvidyas_stripe_pk');
			$stripeSK = get_option('rwvidyas_stripe_sk');
			
    		\Stripe\Stripe::setApiKey($stripeSK);
			$intent = \Stripe\PaymentIntent::create([
				'amount' => $newval,
				'currency' => 'gbp',
				'metadata' => ['rm_video' => $vidya_perm,'customer' => $customer],
				'receipt_email' => $customer_email,
			]);

			$client_secret = $intent->client_secret;


			//if user is logged in but not purchased video
			$output .="
			<div class='rm_videos_video' id='rm_videos_video_n'><div id='rm_videos_video_n_text'>
			<p>
				You need to purchase this video in order to view it!
			</p>
			<p>
				The video costs <b>&pound;$price</b>.
			</p>
			
			
			<div class='sr-root'>
      <div class='sr-main'>
        <form id='payment-form' class='sr-payment-form'>
          <div class='sr-combo-inputs-row'>
            <div class='sr-input sr-card-element' id='card-element'></div>
          </div>
          <div class='sr-field-error' id='card-errors' role='alert'></div>
          <button id='submit'>
            <div class='spinner hidden' id='spinner'></div>
            <span id='button-text'>Pay</span><span id='order-amount'></span>
          </button>
		  <input type='hidden' id='client_secret' value='$client_secret'>
		  <input type='hidden' id='rwvidyas_stripe_pk' value='$stripePK'>
        </form>
        <div class='sr-result hidden'>
          <p>Payment completed<br /></p>
        </div>
      </div>
    </div>
			
			
			</div>
			</div>";
		}else{
			$output .="
			<div class='rm_videos_video' id='rm_videos_video_n'><div id='rm_videos_video_n_text'>
			<p>
				You need to purchase this video in order to view it!
			</p>
			<p>
				Log in to purchase this video.
			</p></div></div>
			";
		}
		
	}
    
    $output .="<div id='rm_video_info'>
		<p>$desc</p>
	</div>";
	
	return $output;
	
    
    
}



