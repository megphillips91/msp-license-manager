<?php
namespace MSP_License_Pro;
use \MSP_License;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once(plugin_dir_path( __FILE__ ).'class-license.php');

/**
 * Verifies remote posts
 * @param  [type] $timestamp         [description]
 * @param  [type] $token             [description]
 * @param  [type] $apikey            [description]
 * @param  [type] $request_signature [description]
 * @return [type]                    [description]
 */
function verify_mg($timestamp, $token, $apikey, $request_signature){
	$combined = $timestamp.$token;
	$calc_signature= hash_hmac('SHA256', $combined, $apikey);
	if($request_signature == $calc_signature) {
		return TRUE;
	} else {return FALSE;}
}

/**
 * Begin the response
 *
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  global $wpdb;
  $response = array();
  $response['method'] = 'post';


//verify for security
  $verified =  verify_mg($_REQUEST['timestamp'], $_REQUEST['token'], $_REQUEST['license_key'], $_REQUEST['signature']);

  $response['verified'] = $verified;


  if($verified){
    $qry = "select * from wp_msp_licenses where license_key = '".$_REQUEST['license_key']."' LIMIT 1";
    $license = $wpdb->get_row($qry);
    $response['license'] = $license;


    //if no license found
    if(!$license){
      $response['license_status']='not found';
			$response = (object)$response;
	    echo json_encode($response);
			die();

    //license is found
    } else {
      $msp_license = new MSP_License('id', $license->id);
      $response['msp_license'] = $msp_license;

    }

    //test license date
    $date = new DateTime(NULL, new DateTimeZone(get_option('timezone_string')));
    if($date >= $msp_license->license_expiration){
      $response['license_status']='expired';
    } else {
      $response['license_date']='within_range';
    }

    //test domain
    if($msp_license->domain != $_REQUEST['host']){
      if($msp_license->domain == NULL){
        $msp_license->set_domain($_REQUEST['host']);
        $response['domain_status'] = 'domain_activated';
        $response['license_status'] = 'active';
      } else {
        $response['domain_status'] = 'domain_mismatch';
        $response['license_status'] = 'domain_mismatch';
      }
    } else {
			$response['domain_status'] = 'domain_match';
      $response['license_status'] = 'active';
    }
    $response = (object)$response;
    echo json_encode($response);
    die();

    //not verified
  } else {
		http_response_code(401);
    $response['verification'] = 'failed';
    $response = (object)$response;
    echo json_encode($response);
		echo 'verification failed';
    die();
  }

}//is a post

?>
