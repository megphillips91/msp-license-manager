<?php
/**
 * Plugin Name: MSP License Manager
 * Plugin URI: http://msp-media.org/
 * Description: Manage License for msp media products
 * Author: megphillips91
 * Author URI: http://msp-media.org/
 * Version: 1.1.2
 * License: GPL2+
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

 /*
 MSP License Manager is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.

 Charter Boat Bookings is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Charter Boat Bookings. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 */
namespace MSP_License_Pro;



 // Exit if accessed directly.
 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 /**
  * Include plugin files
  */
 require_once plugin_dir_path( __FILE__ ) . 'class-license.php';
 require_once plugin_dir_path( __FILE__ ) . 'upgrade_endpoint.php';

 //require_once plugin_dir_path( __FILE__ ) . 'class-notifications.php';



//activate
/**
 * =======================================
 * ACTIVATION HOOK
 * functions to be called on activation
 * find file includes/activate
 * =======================================
 */
register_activation_hook( __FILE__, __NAMESPACE__.'\\msp_maybe_create_tables' );

function msp_maybe_create_tables(){
	global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$charset_collate = $wpdb->get_charset_collate();

		//=== listings table
		$table_name = $wpdb->prefix . 'msp_licenses';
		$sql = "CREATE TABLE $table_name (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      order_id int(11) DEFAULT NULL,
      product_id int(20) DEFAULT NULL,
      item_id int(20) DEFAULT NULL,
      license_key varchar(100) DEFAULT NULL,
      issue_date datetime DEFAULT NULL,
      license_length int(5) DEFAULT NULL,
      domain varchar(300) DEFAULT NULL,
		  PRIMARY KEY (id)
		) $charset_collate;";
		maybe_create_table($table_name, $sql );
  }

/**
 * =================
 * Helper functions
 * general helper functions useful throughout the plugin
 * ==================
 */


function is_msp_licensed($item_id){
  $product_id = $item->get_product_id();
  return has_term('Annual Products', 'product_cat', $product_id);
}





/**
 * =================
 * Testing Stuff
 * just using this space to test some stuff
 * ==================
 */
add_shortcode('test_git_api', __NAMESPACE__.'\\test_git_api');

function test_git_api(){
  $body = array(

  );

  $args = array(
    'body'=>$body
  );
  $url = 'https://api.github.com/users/megphillips91/repos';
  $response = wp_remote_get($url, $args);
  $body = wp_remote_retrieve_body($response);
  echo '<pre>'; var_dump($body); echo '</pre>';
}
