<?
//Edit admin list layout to include custom fields on custom post type
add_action("manage_rm_videos_posts_custom_column",  "rwvidyas_custom_columns");
add_filter("manage_edit-rm_videos_columns", "rwvidyas_edit_columns");

function rwvidyas_edit_columns($columns){
  $columns = array(
    "cb" => "<input type='checkbox' />",
    "title" => "Video Title",
    "description" => "Description",
    "price_meta" => "Price",
    "vimeo_url" => "Vimeo URL",
  );
  return $columns;
}
function rwvidyas_custom_columns($column){
  global $post;
  switch ($column) {
    case "description":
      the_excerpt();
      break;
    case "price_meta":
      $custom = get_post_custom();
      echo "&pound;".$custom["price_meta"][0];
      break;
    case "vimeo_url":
      $custom = get_post_custom();
      echo $custom["vimeo_url"][0];
      break;
  }
}
//Add purchases page to custom post type menu




//Settings Page
function rwvidyas_register_settings() {
  add_option( 'rwvidyas_stripe_pk', '');
  add_option( 'rwvidyas_stripe_sk', '');
  register_setting( 'rwvidyas_stripe_api', 'rwvidyas_stripe_pk', '' );
  register_setting( 'rwvidyas_stripe_api', 'rwvidyas_stripe_sk', '' );
}
add_action( 'admin_init', 'rwvidyas_register_settings' );

function rwvidyas_register_options_page() {
  add_options_page('RW Vidyas Settings', 'RW Vidyas Settings', 'manage_options', 'rm_video', 'rwvidyas_options_page');

  add_submenu_page(
    'edit.php?post_type=rm_videos',
    __( 'Video Purchases', 'rm_video' ),
    __( 'Video Purchases', 'rm_video' ),
    'manage_options',
    'rm_video',
    'rwvidyas_purchases_page'
);
}
add_action('admin_menu', 'rwvidyas_register_options_page');

function rwvidyas_purchases_page() {
  ?>
  <div>
  <?php screen_icon(); ?>
  <h1 class="wp-heading-inline">Video Purchases</h1>
 <?php
 echo(list_vidya_purchases());
}

function rwvidyas_options_page() {
?>
  <div>
  <?php screen_icon(); ?>
  <h1 class="wp-heading-inline">RW Vidyas Settings</h1>
  <form method="post" action="options.php">
  <?php settings_fields( 'rwvidyas_stripe_api' ); ?>
  <h3>Stripe API Settings</h3>
  <p>Place your api keys here.  Remember to replace test keys with live keys once your application is live.</p>
  <table>
  <tr valign="top">
  <th scope="row"><label for="rwvidyas_stripe_pk">Stripe Publishable Key:</label></th>
  <td><input type="text" id="rwvidyas_stripe_pk" name="rwvidyas_stripe_pk" value="<?php echo get_option('rwvidyas_stripe_pk'); ?>" /></td>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="rwvidyas_stripe_sk">Stripe Secret Key:</label></th>
  <td><input type="text" id="rwvidyas_stripe_sk" name="rwvidyas_stripe_sk" value="<?php echo get_option('rwvidyas_stripe_sk'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}






//Create the webhook
add_action('update_option_rwvidyas_stripe_sk','rwvidyas_create_webhook_endpoint');

function rwvidyas_create_webhook_endpoint(){
  require_once('vendor/autoload.php');
  $stripeSK = get_option('rwvidyas_stripe_sk');
  $endpointURL =  get_site_url()."/wp-json/rm_videos/v1/endpoint";
  if($stripeSK != ""){
    \Stripe\Stripe::setApiKey($stripeSK);
    \Stripe\WebhookEndpoint::create([
      'url' => "$endpointURL",
      'enabled_events' => [
        "payment_intent.payment_failed",
        "payment_intent.processing",
        "payment_intent.succeeded"
      ],
    ]);
    echo "Webhook created successfully.";
  }
}