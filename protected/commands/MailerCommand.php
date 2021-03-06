
<?php

class MailerCommand extends CConsoleCommand{

	public function actionTest(){
    echo "Test succeded on ".date("d M Y H:i:s");
    return 0;
	}  
  
  /**
   * generates weekly reports for users that want them
   */
	public function actionWeekly(){
    return 0;
    
    $message = new YiiMailMessage;
    //$message->view = 'newsletter';
    $message->setBody('En testni mail', 'text/html');
    $message->subject = "subject";

    // get all users
    $criteria = new CDbCriteria();
    $criteria->condition = 'newsletter=1'; // new
    $users = User::model()->findAll($criteria);
    foreach ($users as $user){
      $message->addBcc($user->email);
    }

    $message->from = Yii::app()->params['adminEmail'];
    Yii::app()->mail->send($message);
    
    //return 0; // all OK // not needed
	}
  
  /**
   * notify all users that haven't been active for a while
   */
  public function actionNoActivity(){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->from = Yii::app()->params['noreplyEmail'];    
    
    $users = User::model()->findAll("(lastvisit_at + INTERVAL 2 MONTH) < CURDATE() AND newsletter=1");
    $c = 0;
    foreach ($users as $user){
      $lastvisit_at = strtotime($user->lastvisit_at);

      if ($lastvisit_at < strtotime('-1 year')) continue;     // we give up after a year
      //if ($lastvisit_at > strtotime('-2 month')) continue;    // don't notify before inactive for 2 months
 
      if (!
          (($lastvisit_at >= strtotime('-3 month')) || 
          (($lastvisit_at >= strtotime('-7 month')) && ($lastvisit_at < strtotime('-6 month'))) || 
          (($lastvisit_at >= strtotime('-12 month')) && ($lastvisit_at < strtotime('-11 month'))) )
         ) continue;
      
//set mail tracking
      $mailTracking = mailTrackingCode($user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'no-activity-reminder';
      $ml->user_to_id = $user->id;
      $ml->save();
    
      $message->subject = $user->name." did you forget about us?";
      
      //$activation_url = '<a href="'.absoluteURL()."/user/registration?id=".$user->key.'">Register here</a>';
      //
      //$activation_url = mailButton("Register here", absoluteURL()."/user/registration?id=".$user->key,'success',$mailTracking,'register-button');
      $content = "Since your last visit we got some awesome new ".mailButton('projects', absoluteURL().'/project/discover','link', $mailTracking,'projects-button')." and interesting ".
                  mailButton('people', absoluteURL().'/person/discover','link', $mailTracking,'people-button')." signup at Cofinder.
                  <br /><br />
                  Why don't you check them out on ".mailButton('Cofinder', 'http://www.cofinder.eu','success', $mailTracking,'cofinder-button');
      
      $message->setBody(array("content"=>$content,"email"=>$user->email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($user->email);
      Yii::app()->mail->send($message);
      $c++;
    }
    
    Slack::message("CRON >> No activity reminders: ".$c);
  }
  
  
  /**
   * generates monthly reports for users that want them
   */
	public function actionSuggestions(){
    return 0;
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $invites = Invite::model()->findAll("NOT ISNULL(`key`)");
    foreach ($invites as $user){
      
      $create_at = strtotime($stat->time_invited);
      if ($create_at < strtotime('-8 week') || $create_at >= strtotime('-1 day')) continue;     
      if (!
          (($create_at >= strtotime('-1 week')) || 
          (($create_at >= strtotime('-4 week')) && ($create_at < strtotime('-3 week'))) || 
          (($create_at >= strtotime('-8 week')) && ($create_at < strtotime('-7 week'))) )
         ) continue;
      
      //set mail tracking
      $mailTracking = mailTrackingCode($user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'suggestion-created';
      $ml->user_to_id = $user->id;
      $ml->save();

      $message->subject = "We are happy to see you interested in Cofinder";  // 11.6. title change
      
      $content = "This is just a friendly reminder to activate your account on Cofinder.
                  <br /><br />
                  Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself.
                  <br /><br />If we got your attention you can !";
      
      $message->setBody(array("content"=>$content,"email"=>$user->email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($user->email);
      Yii::app()->mail->send($message);
    }
    return 0;
	}
  
  public function actionNotifyUnreadMsg(){
    $message = new YiiMailMessage;
    $message->view = 'system';
    
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $unreadMails = MailLog::model()->findAll("type LIKE 'user-message' AND ISNULL(time_open)");
    $unreadMails = Notification::model()->bynotifyat()->findAll("viewed = 0 AND type = 1");
    $users = array();
    $last_date = '';
    foreach ($unreadMails as $mailLog){
      
      $create_at = strtotime($mailLog->user->lastvisit_at);
      if ($create_at < strtotime('-2 month')) continue; // older than 4 months don't notify
      
      if (!isset($users[$mailLog->user_id])){
        $users[$mailLog->user_id]['count'] = 1;
        $users[$mailLog->user_id]['email'] = $mailLog->user->email;
        $users[$mailLog->user_id]['name'] = $mailLog->user->name;
      }else $users[$mailLog->user_id]['count']++;
      $users[$mailLog->user_id]['date'] = $mailLog->notify_at;
    }
    $c=0;
    // notify all that have missing messages
    foreach ($users as $key => $user){
      
      $last_msg = date("Y-m-d H",strtotime($user['date']));
      
      if ( !($last_msg == date("Y-m-d H",strtotime('-2 hour')) || $last_msg == date("Y-m-d H",strtotime('-1 day')) || 
          $last_msg == date("Y-m-d H",strtotime('-3 days')) || $last_msg == date("Y-m-d H",strtotime('-8 days')) || 
          $last_msg == date("Y-m-d H",strtotime('-2 weeks')) || $last_msg == date("Y-m-d H",strtotime('-3 weeks')) || 
          $last_msg == date("Y-m-d H",strtotime('-4 weeks')) || $last_msg == date("Y-m-d H",strtotime('-5 weeks')) ||
          $last_msg == date("Y-m-d H",strtotime('-7 weeks'))) ) continue;
      
          
      //set mail tracking
      $mailTracking = mailTrackingCode($key);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'message-reminder';
      $ml->user_to_id = $key;
      $ml->save();
    
      if ($user['count'] == 1) $message->subject = $user['name']." you have 1 unread message";
      else $message->subject = $user['name']." you have ".$user['count']." unread messages";
      
      //$activation_url = '<a href="'.absoluteURL()."/user/registration?id=".$user->key.'">Register here</a>';
      $content = "Hi ".$user['name'].", you have some unread messages waiting for you on Cofinder.
                  <br />
                  People will take you more seriously if you reply as soon as possible. ".mailButton("Read them now", absoluteURL()."/message",'success',$mailTracking,'message-button');
      
      $message->setBody(array("content"=>$content,"email"=>$user['email'],"tc"=>$mailTracking), 'text/html');
      $message->setTo($user['email']);
      //$message->setTo('bercium@gmail.com');
      Yii::app()->mail->send($message);
      //break;
      $c++;
    }
    if ($c > 0) Slack::message("CRON >> Unread msg notifications: ".$c);
    
    return 0;
  }
  
  // do this every first wednesday of month
  public function actionNotifyToJoin(){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = "We are happy to see you soon on Cofinder";  // 11.6. title change
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $invites = Invite::model()->findAll("NOT ISNULL(`key`)");
    $c = 0;
    foreach ($invites as $user){
      
      $create_at = strtotime($user->time_invited);
      if ($create_at < strtotime('-8 week') || $create_at >= strtotime('-1 day')) continue;     
      if (!
          (($create_at >= strtotime('-1 week')) || 
          (($create_at >= strtotime('-4 week')) && ($create_at < strtotime('-3 week'))) || 
          (($create_at >= strtotime('-8 week')) && ($create_at < strtotime('-7 week'))) )
         ) continue;      
      
      //set mail tracking
      $mailTracking = mailTrackingCode($user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'invitation-reminder';
      $ml->user_to_id = $user->id;
      $ml->save();
    
      //$activation_url = '<a href="'.absoluteURL()."/user/registration?id=".$user->key.'">Register here</a>';
      $activation_url = mailButton("Register here", absoluteURL()."/user/registration?id=".$user->key,'success',$mailTracking,'register-button');
      $content = "This is just a friendly reminder to activate your account on Cofinder.
                  <br /><br />
                  Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself.
                  <br /><br />If we got your attention you can ".$activation_url."!";
      
      $message->setBody(array("content"=>$content,"email"=>$user->email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($user->email);
      Yii::app()->mail->send($message);
      $c++;
    }
    Slack::message("CRON >> Invite to join reminders: ".$c);
    return 0;
  }
  
  
  // do this every first of month
  public function actionNotifyHiddenProfiles(){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $hidden = UserStat::model()->findAll("completeness < :comp",array(":comp"=>PROFILE_COMPLETENESS_MIN));
    $c = 0;
    foreach ($hidden as $stat){
      //set mail tracking
      if ($stat->user->status == 0) continue; // skip non active users
      if (strtotime($stat->user->lastvisit_at) < strtotime('-2 month')) continue; // skip users who haven't been on our platform for more than 2 months
      
      //echo $stat->user->email." - ".$stat->user->name." ".$stat->user->surname." ".$stat->user->lastvisit_at." your profile is not visible!";
      //continue;
      
      $mailTracking = mailTrackingCode($stat->user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'hidden-profiles';
      $ml->user_to_id = $stat->user->id;
      $ml->save();
      
      $email = $stat->user->email;
      $message->subject = $stat->user->name." your Cofinder profile is hidden!";
      
      $content = 'Your profile on Cofinder is not visible due to lack of information you provided. 
                  If you wish to be found we suggest you take a few minutes and '.
              mailButton("fill it up", absoluteURL('/profile'),'success',$mailTracking,'fill-up-button');
      
      $message->setBody(array("content"=>$content,"email"=>$email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($email);
      Yii::app()->mail->send($message);

      Notifications::setNotification($stat->user_id,Notifications::NOTIFY_INVISIBLE);
      $c++;
    }
    Slack::message("CRON >> Hidden profiles: ".$c);
    return 0;
  }
  
  // do this every first of month
  public function actionNotifyUnExeptedProfiles(){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $hidden = UserStat::model()->findAll("completeness < :comp",array(":comp"=>PROFILE_COMPLETENESS_MIN));

    $c = 0;
    foreach ($hidden as $stat){
      //set mail tracking
      if ($stat->user->status != 0) continue; // skip active users
      if ($stat->user->newsletter == 0) continue; // skip those who unsubscribed
      if ($stat->user->lastvisit_at != '0000-00-00 00:00:00') continue; // skip users who have already canceled their account
      
      //echo $stat->user->name." - ".$stat->user->email.": ".$stat->user->create_at." (".date('c',strtotime('-4 week'))."     ".date('c',strtotime('-3 week')).")<br />\n";
      $create_at = date("Y-m-d H",strtotime($stat->user->create_at));
      //$create_at_hour = date("Y-m-d H",strtotime($stat->user->create_at));
      /*if ($create_at < strtotime('-8 week') || $create_at >= strtotime('-1 day')) continue;      
      if (!
          (($create_at >= strtotime('-1 week')) || 
          (($create_at >= strtotime('-4 week')) && ($create_at < strtotime('-3 week'))) || 
          (($create_at >= strtotime('-8 week')) && ($create_at < strtotime('-7 week'))) )
         ) continue;*/
      if ( !($create_at == date("Y-m-d H",strtotime('-2 hour')) || $create_at == date("Y-m-d H",strtotime('-1 day')) || 
          $create_at == date("Y-m-d H",strtotime('-3 days')) || $create_at == date("Y-m-d H",strtotime('-8 days')) || 
          $create_at == date("Y-m-d H",strtotime('-14 day')) || $create_at == date("Y-m-d H",strtotime('-21 day')) || 
          $create_at == date("Y-m-d H",strtotime('-28 day'))) ) continue;
      //echo $stat->user->email." - ".$stat->user->name." your Cofinder profile is moments away from approval!";

      //echo "SEND: ".$stat->user->name." - ".$stat->user->email.": ".$stat->user->create_at." (".$stat->completeness.")<br />\n";
      //echo 'http://www.cofinder.eu/profile/registrationFlow?key='.substr($stat->user->activkey,0, 10).'&email='.$stat->user->email;

      //continue;
      //set mail tracking
      $mailTracking = mailTrackingCode($stat->user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'registration-flow-reminder';
      $ml->user_to_id = $stat->user->id;
      $ml->save();

      $email = $stat->user->email;
      $message->subject = $stat->user->name." your Cofinder account is almost approved"; // 11.6. title change

      $content = "We couldn't approve your profile just yet since you haven't provided enough information."
              . "Please fill your profile and we will revisit your application.".
              mailButton("Do it now", absoluteURL().'/profile/registrationFlow?key='.substr($stat->user->activkey,0, 10).'&email='.$stat->user->email,'success',$mailTracking,'fill-up-button');

      $message->setBody(array("content"=>$content,"email"=>$email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($email);
      Yii::app()->mail->send($message);

      Notifications::setNotification($stat->user_id,Notifications::NOTIFY_INVISIBLE);
      $c++;
    }
    if ($c > 0) Slack::message("CRON >> UnExcepted profiles: ".$c);
    return 0;
  }  
  
}
