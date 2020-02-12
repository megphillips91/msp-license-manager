<?php
namespace MSP_License_Pro;
use \MSP_License;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

add_action( 'rest_api_init', function () {
  register_rest_route( 'msp_license_manager/v1', '/charter_bookings_pro', array(
    'methods' => 'GET',
    'callback' => __NAMESPACE__.'\\upgrade_endpoint',
  ) );
} );

function upgrade_endpoint($data){
  $response = array();
  $response['license_key'] = $data['license_key'];
  $response['published_at'] = get_option('cb_pro_published_at');
  $response['tag'] = get_option('cb_pro_tag');
  $response['tag_name'] = get_option('cb_pro_tag');
  $response['body'] = get_option('cb_pro_tag_body');
  $response['zipball_url'] = 'https://msp-media.org/wp-content/plugins/msp-license-manager/charter-bookings-pro-zipball.php';
  return $response;
}





?>
