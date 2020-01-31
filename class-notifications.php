<?php
namespace MSP_License_Pro;

/**
 * License Notification sets up to send emails through wp_mail
 * @param (array) $users is an array('display_name', 'user_email');
 * @param (string) tell_admins | tell_meg |
 */

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


 ?>
