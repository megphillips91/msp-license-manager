<?php
namespace MSP_License_Pro;
use \MSP_License;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

/**
 * License Notification sets up to send emails through wp_mail
 * @param object $recipient is a WP User object
 * @param string $from_email is a valid email for the reply to
 * @param object $msp_license is the license object
 * @param array $attachments is an array of attachments
 */

add_shortcode('test_license_notification', __NAMESPACE__.'\\test_license_notification_shortcode');

function test_license_notification_shortcode(){
  $user = get_user_by('ID', 1);
  $msp_license = new MSP_License('id', 8);
  $message = new Domain_Change_Confirmation($user, 'meg.phillips@msp-media.org', $msp_license);
  $message->send();
}

class License_Notification {
  public $to;
  public $first_name;
  public $message;
  public $subject;
  private $msp_license;
  public $attachments;

  public function __construct($recipient, $from_email, $msp_license, $attachments = NULL){
    $this->license = $msp_license;
    $this->headers = array('reply_to'=>$from_email, 'cc'=>'meg.phillips@msp-media.org');
    $this->set_user_fields($recipient);
    $this->set_license_dates();
    $this->set_message();
    $this->set_subject();
    // change content type to html from plain text
    add_filter( 'wp_mail_content_type', function (){
      return "text/html";
      } );
  }

  private function set_content_type_header(){
      return "text/html";
  }

  public function send(){
    $sent = wp_mail(
      $this->to,
      $this->subject,
      $this->message,
      $this->headers,
      $this->attachments
    );
    return $sent;
  }

  private function set_license_dates(){
    $date_object = new DateTime($this->license->issue_date, new DateTimeZone(get_option('timezone_string')));
    $this->issue_date = $date_object->format('F d, Y');
    $this->expiration_date = $this->license->license_expiration->format('F d, Y');
  }

  protected function set_user_fields($user){
    $name = explode(' ', $user->data->display_name);
    $this->first_name = $name[0];
    $this->to = $user->data->user_email;
  }

  protected function set_subject(){
    $subject = '';
    $this->subject = $subject;
  }

  protected function set_message(){
    $html = '';
    $this->message = $html;
  }

}//end base parent class declaration


class License_Expired_Email extends License_Notification {

  protected function set_message(){
    $html = '<p>'.$this->first_name.',<p>';
    $html .= '<p>When we last checked, your license for Charter Bookings Pro was issued on '.$this->issue_date.' for a term of 365 days and the expiration date is '.$this->expiration_date.'. Just wanted to check in. </p></p>We hope you Charter Bookings is working great for you. Are you planning to continue using Charter Bookings Pro?';
    $html .= '<p>Meg Phillips<br>Author Charter of Bookings Pro<br>meg.phillips@msp-media.org</p>';
    $this->message = $html;
  }

  protected function set_subject(){
    $subject = 'Charter Bookings Pro License Expiration';
    $this->subject = $subject;
  }

}

class Invalid_Domain_Email extends License_Notification {

  protected function set_message(){
    $this->license->set_billing_information();
    $html = '<p>'.$this->first_name.',<p>';
    $html .= '<p>When we last checked, your license for Charter Bookings Pro was first activated on '.$this->license->domain.'. Just wanted to check in. </p></p>We hope you Charter Bookings is working great for you, but it looks like this website does not match the domain we have on file.</p>
    <p>Your license is valid for one domain at a time. If you would like to change the domain on which you are using Charter Bookings Pro, that is no problem. </p>
    <p>'.$this->license->billing_information['first_name'].' '.$this->license->billing_information['last_name'].' purchased the license for Charter Bookings Pro. We will email '.$this->license->billing_information['first_name'].' and confirm that it is okay want to use the license on this domain instead of the original domain. <p><p>If you feel you have received this email in error, please reply and someone will help you sort out this issue. ';
    $html .= '<p>Thank you, <br>Meg Phillips<br>Author of Charter Bookings Pro<br>meg.phillips@msp-media.org</p>';
    $this->message = $html;
  }

  protected function set_subject(){
    $subject = 'Charter Bookings Pro Domain Mismatch';
    $this->subject = $subject;
  }

}

class Domain_Change_Confirmation extends License_Notification{

  protected function set_user_fields($user){
    $this->license->set_billing_information();
    $this->first_name = $this->license->billing_information['first_name'];
    $this->to = $this->license->billing_information['email'];
  }

  protected function set_message(){
    $html = '<p>'.$this->license->billing_information['first_name'].',</p>';
    $html .= '<p>Your License for Charter Bookings Pro is for one domain. The license we are emailing you regarding was first activated for '.$this->license->domain.'. This email is to notify you that another domain is trying to use your license. </p>';
    $html .= '<p>To resolve this matter, you could purchase another license if you need to run two websites or confirm that you want your license to be moved to a different domain.</p> <p>Please respond to us as soon as possible to prevent your Charter Bookings Pro from being deactivated. </p>';
    $html .= '<p>Thank you, <br>Meg Phillips<br>Author of Charter Bookings Pro<br>meg.phillips@msp-media.org</p>';
    $this->message = $html;

  }

  protected function set_subject(){
    $subject = 'Charter Bookings Pro - Confirm Domain Change';
    $this->subject = $subject;
  }


}

class Licesnse_Not_Found_Email extends License_Notification {

  protected function set_message(){
    $this->license->set_billing_information();
    $html = '<p>'.$this->first_name.',</p>';
    $html .= '<p>It appears you tried to validate Charter Bookings Pro with a license key that does not match our records. Please be sure to copy and paste the license key rather than trying to type it in. </p><p>You can always access your downloads and license key at <a ref="https://msp-media.org/my-account/" >Your Account: https://msp-media.org/my-account/ </a></p><p>If you are having trouble activating your license, please respond to this email and someone from our team can help get you going. </p>';
    $html .= '<p>Thank you, <br>Meg Phillips<br>Author of Charter Bookings Pro<br>meg.phillips@msp-media.org</p>';
    $this->message = $html;
  }

  protected function set_subject(){
    $subject = 'Charter Bookings Pro License Key Not Found';
    $this->subject = $subject;
  }

}

 ?>
