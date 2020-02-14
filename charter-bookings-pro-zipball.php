<?php
namespace MSP_License_Pro;
use \MSP_License;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once(plugin_dir_path( __FILE__ ).'class-license.php');

if(!empty($_GET['license_key'])){
  $license = new MSP_License('key', $_GET['license_key']);
  $active = $license->check_date();
  //$valid = $license->check_host($_REQUEST['host']);
  if($active){
    $file = '/home/mspmedia/premium_products/charter-bookings-pro.zip';
    header('Content-Description: File Transfer');
    header("Content-disposition: attachment;");
    header('Content-type: application/zip');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    //ob_clean();
    flush();
    readfile($file);
    exit;
  } else {
    $response = array();
    $response['code'] = '403';
    $response['message'] = 'the license key you provided is either expired or not linked with the request host';
    echo json_encode($response);
  }
} else {
  $response = array();
  $response['code'] = '403';
  $response['message'] = 'The license key you provided is missing or invalid. Please provide a valid License Key and try again.';
  echo json_encode($response);
}
/*
$file = '/home/mspmedia/premium_products/charter-bookings-pro.zip';
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($file));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
ob_clean();
flush();
readfile($file);
exit;
*/
?>
