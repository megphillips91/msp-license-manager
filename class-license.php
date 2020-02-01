<?php

add_action('woocommerce_payment_complete', 'msp_issue_license');
 function msp_issue_license ($order_id){
   $order_items = new MSP_License_Order_Items($order_id);
   foreach($order_items->licensed_items as $item){
     $qty = $item->get_quantity();
     for($x = 1; $x <= $qty; $x++){
     $license = new MSP_License_factory($order_id, $item);
     $item_id = $item->get_id();
     $meta_key = 'License';
     wc_add_order_item_meta( $item_id, $meta_key, $license->key, $unique = false );
    }
   }
 }

/**
 * License Order Items
 * loop through wc order items and identify which are licensed items. create an array of items which are licensed_items
 */
class MSP_License_Order_Items {
  public $licensed_items;

  public function __construct($order_id){
    $order = wc_get_order($order_id);
    $this->licensed_items = array();
    if($order){
      $this->order_id = $order_id;
      $items = ($order)? $order->get_items() : NULL;
      foreach($items as $item){
        $product_id = $item->get_product_id();
        if(has_term('Annual Products', 'product_cat', $product_id)){
          $this->licensed_items[] = $item;
        }
      }
   }

  }
} //end class


/**
* MSP License class
*/
class MSP_License {
 public $id;
 public $product_id;
 public $order_id;
 public $license_key;
 public $issue_date;
 public $license_length;
 public $domain;
 public $license_expiration;

 public function __construct($field, $value){
   switch($field){
     case 'id':
       $this->get_license_by_id($value);
       $this->set_license_expiration();
       break;
   }
 }

 public function get_license_by_id($id){
   global $wpdb;
   $license = $wpdb->get_row("select * from wp_msp_licenses where id=".$id);
   foreach($license as $key=>$value){
     $this->$key = $value;
   }
 }
 /**
  * set expiration date of license
  *
  * @return object php datetime object
  */
 public function set_license_expiration(){
   $date = new DateTime(NULL, new DateTimeZone(get_option('timezone_string')));
   $date->add(new DateInterval("P365D"));
   $this->license_expiration = $date;
 }

 public function set_domain($host){
   global $wpdb;
   $wpdb->update(
     'wp_msp_licenses',
     array('domain'=>$host),
     array('id'=>$this->id)
   );
 }

 public function set_billing_information(){
   $order = wc_get_order($this->order_id);
   $billing_information = array();
   $billing_information['email'] = $order->get_billing_email();
   $billing_information['first_name'] = $order->get_billing_first_name();
   $billing_information['last_name'] = $order->get_billing_last_name();
   $this->billing_information = $billing_information;
 }



} //end license


/**
 * MSP Licenses
 * Array of all licenses on one order
 * @param int order_id
 */
class MSP_Licenses {
 public $licenses;

 public function __construct($field, $value){
 }

 public function get_licenses_by_order_id($id){
   global $wpdb;
   $licenses = $wpdb->get_results("select * from wp_msp_licenses where order_id=".$id);

   foreach($license as $license){
     $licenses[] = new MSP_License('id', $license->id);
   }
 }

}


/**
*  MSP_License Factory
*
* @param object order_item is item object not id
*/
class MSP_License_factory {
  public $product_id;
  public $item_id;
  public $order_id;
  public $key;
  public $issue_date;
  public $license_length;
  public $domain;

  public function __construct($order_id, $order_item){
    $order = wc_get_order($order_id);
    $this->item_id = $order_item->get_id();
    $this->billing_email = $order->get_billing_email();
    $this->product_id = $order_item->get_product_id();
    $this->order_id = $order_id;
    $this->generate_key();
    date_default_timezone_set('America/New_York');
    $this->issue_date = date('Y-m-d H:i:s');
    $this->commit();
  }

  private function generate_key(){
    $title = get_the_title($this->product_id);
    $raw = time().$title.'msp-media'.$this->billing_email;
    $key = sha1($raw);
    $this->key = $key;
  }

  private function commit(){
    global $wpdb;
    $wpdb->insert(
      'wp_msp_licenses',
      array(
        'order_id'=>$this->order_id,
        'product_id'=>$this->product_id,
        'item_id'=>$this->item_id,
        'license_key'=>$this->key,
        'issue_date'=>$this->issue_date,
        'license_length'=>'365'
      )
    );
  }
} //end license factory


 ?>
