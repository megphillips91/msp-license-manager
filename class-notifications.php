<?php
namespace MSP_License_Pro;

/**
 * License Notification sets up to send emails through wp_mail
 * @param object $recipient is a WP User object
 * @param string $from_email is a valid email for the reply to
 * @param array $attachments is an array of attachments
 */

add_shortcode('test_license_notification', 'test_license_notification_shortcode');

function test_license_notification_shortcode(){
  $user = get_user_by('ID', 1);
  $message = new License_Expired_Email($user, 'meg.phillips@msp-media.org');
  echo '<pre>'; var_dump($message); echo '</pre>';
}

class License_Notification {
  public $to;
  public $first_name;
  public $reply_to;
  public $message;
  public $subject;
  public $attachments;

  public function __construct($recipient, $from_email, $attachments = NULL){
    $this->headers = array('reply_to'=>$this->from_email, 'cc'=>'meg.phillips@msp-media.org');
    $this->set_user_fields();
    $this->set_message();
    $this->set_subject();
  }
/*
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
  */

  private set_user_fields(){
    $name = explode(' ', $user->data->display_name);
    $this->first_name = $name[0];
    $this->to = $user->data->user_email;
  }

  private function set_subject(){
    $subject = '';
    $this->subject = $subject;
  }

  private function set_message(){
    $html = '';
    $this->html = $html;
  }


}//end class


class License_Expired_Email extends License_Notification {

  private function set_message(){
    $html = '<p>'.$this->first_name.',<p>';
    $html .= '<p>When we last checked, your license for Charter Bookings Pro was issued on '.$issue_date.' for a term of 365 days. Just wanted to check in. </p></p>Are you planning to continue using Charter Bookings Pro?';
    $html .= '<p>Meg Phillips<br>>Author Charter Bookings Pro<br>meg.phillips@msp-media.org</p>';
    $this->message = $html;
  }

  private function set_subject(){
    $subject = 'Charter Bookings Pro License Expiration';
    $this->subject = $subject;
  }

}

class Invalid_Domain_Email extends License_Notification {

}



/**
 */
/*

class License_Notification {
  private set_message;

  public function __construct($type = NULL, $users){
      $this->type = $type;
  }

  public function send() {
    foreach ($users as $user){
      $to = $user->user_email;
      $name = $user->display_name;
      $setfunction = 'set_'.$this->type;
      $message = $this->$setfunction();
      $headers = array();
      $headers['reply_to'] = 'meg.phillips@msp-media.org';
      return wp_mail(
        $to,
        $message['subject'],
        $message['message'],
        $headers
      );
    }
  }


  private function set_tell_admins($name){
    $message = '<p>Hi '.$name.'</p>';
    $message = '<p>It appears that your Charter Bookings Pro license is invalid. Please contact <a href="mailto:meg.phillips@msp-media.org">MSP Media</a> with regard to this issue.  Otherwise, your Pro features could be disabled which could cause site errors. We appreciate your prompt attention to this matter.</p>

    <p>If you have recieved this message in error, we apologize. Your account manager has also been notified and is investigating this issue manually. </p>';

    $message .= '<p>Thank You,<br>Meg Phillips<br>meg.phillips@msp-media.org<br><a href="msp-media.org">MSP-Media.org</a>';
    $subject = 'Charter Bookings Pro License';
    return array('message'=>$message, 'subject'=>$subject);

  }

  private function set_tell_meg(){
    $message = '<strong>'.$_SERVER['HTTP_HOST'].'</strong> appears to be running Charter Bookings Pro with invalid license. Please check and respond.';
    $subject = 'Invalid License for CB Pro';
    return array('message'=>$message, 'subject'=>$subject);
  }

}
*/

 ?>
