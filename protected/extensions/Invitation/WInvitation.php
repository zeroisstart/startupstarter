<?php

class WInvitation extends CWidget
{

    public $renderLayout = true;
  
    public function init()
    {
      $user = User::model()->findByPk(Yii::app()->user->id);
 // send invitations
      if (!empty($_POST['invite-email']) && $user){

        if (!empty($_POST['invite-user-id']) && (strpos($_POST['invite-email'],'@')===false)){
          $invitee = User::model()->findByPk($_POST['invite-user-id']);
          $_POST['invite-email'] = $invitee->email;
        }
        // create invitation
        $invitation = new Invite();
        $invitation->email = $_POST['invite-email'];
        $invitation->sender_id = Yii::app()->user->id;
        $invitation->key = md5(microtime().$invitation->email);

        // invite to idea
        if (!empty($_POST['invite-idea'])){
          $checkUser = UserMatch::model()->findByAttributes(array("user_id"=>$user->id));
          $checkIdea = IdeaMember::model()->findByAttributes(array("idea_id"=>$_POST['invite-idea'], "match_id"=>$checkUser->id));
          
          // if idea exists
          if ($checkIdea){
            $invitation->idea_id = $_POST['invite-idea']; // invite to idea
            $invitee = User::model()->findByAttributes(array("email"=>$invitation->email));
            
            if ($invitee){
              // user is in system
              if ($invitee->id ==  Yii::app()->user->id){
                setFlash("invitationMessage",Yii::t('msg','Unable to send invitation to yourself!'),'alert');
              }else{
                $invitation->receiver_id = $invitee->id;

                if ($invitation->save()){
                  Notifications::setNotification($invitation->receiver_id,Notifications::NOTIFY_PROJECT_INVITE);

                  $mailTracking = mailTrackingCode();
                  $ml = new MailLog();
                  $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
                  $ml->type = 'project-invite';
                  $ml->user_to_id = $invitation->receiver_id;
                  $ml->save();                  
      
                  $idea = IdeaTranslation::model()->findByAttributes(array("idea_id"=>$invitation->idea_id),array('order' => 'FIELD(language_id, 40) DESC'));

                  //$activation_url = '<a href="'.Yii::app()->createAbsoluteUrl('/profile/acceptInvitation')."?id=".$invitation->idea_id.'">Accept invitation</a>';
                  $activation_url = mailButton("Accept invitation", Yii::app()->createAbsoluteUrl('/profile/acceptInvitation')."?id=".$invitation->idea_id, "success", $mailTracking,'accept-invitation-button');
                  $this->sendMail($invitation->email,
                                  "You have been invited to join a project on cofinder", 
                                  $user->name." ".$user->surname." invited you to become a member of a project called '".$idea->title."'".
                                                  "<br /><br />You can accept his invitation inside your cofinder profile or by clicking ".$activation_url."!",
                                  $mailTracking);

                  setFlash("invitationMessage",Yii::t('msg','Invitation to add new member sent.'));
                }else setFlash("invitationMessage",Yii::t('msg','Unable to send invitation! Eather user is already invited or the email you provided is incorrect.'),'alert');
              }
            }else{
              // invite outside the system
              
              if ($user->invitations > 0){
                if ($invitation->save()){
                  $user->invitations = $user->invitations-1;
                  $user->save();
                  
                  $stat = UserStat::model()->findByAttributes(array('user_id'=>$user->id));
                  $stat->invites_send = $stat->invites_send+1;
                  $stat->save();

                  //$idea = IdeaTranslation::model()->findByAttributes(array("idea_id"=>$invitation->idea_id),array('order' => 'FIELD(language_id, 40) DESC'));

                  $invite = Invite::model()->findByAttributes(array('email' => $_POST['invite-email'],'idea_id'=>null));
                  if ($invite){
                    //if self invited already
                    if (!$invite->key){
                      // invite
                      $invite->sender_id = Yii::app()->user->id;
                      $invite->key = md5(microtime().$invitation->email);
                    }
                  }else{
                    // invite user to system
                    $invite = new Invite();
                    $invite->email = $_POST['invite-email'];
                    $invite->sender_id = Yii::app()->user->id;
                    $invite->key = md5(microtime().$invitation->email);
                  }
                  $invite->save();

                  // incognito tracking (no user in system yet)
                  $mailTracking = mailTrackingCode();
                  $ml = new MailLog();
                  $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
                  $ml->type = 'cofinder-invite';
                  $ml->user_to_id = null;
                  $ml->save();

                  //$activation_url = '<a href="'.Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invite->key.'"><strong>Register here</strong></a>';
                  $activation_url = mailButton("Register here", Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invite->key, "success", $mailTracking,'register-button');

                  $this->sendMail($invitation->email,
                                  "You have been invited to join cofinder", 
                                  "We've been hard at work on our new service called cofinder.
                                                  Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself. 
                                                  <br /><br /> <strong>".$user->name." ".$user->surname."</strong> thinks you might be the right person to test our private beta.
                                                  <br /><br /> If we got your attention you can ".$activation_url."!",
                                  $mailTracking);
                  setFlash("invitationMessage",Yii::t('msg','Invitation to add new member sent.'));

                }else setFlash("invitationMessage",Yii::t('msg','Unable to send invitation! Eather user is already invited or the email you provided is incorrect.'),'alert');
              }else setFlash("invitationMessage",Yii::t('msg','You can invite only users in the system to join projects.'),'alert');
            }
           
          }else setFlash("invitationMessage",Yii::t('msg','Not able to invite this person to this project.'),'alert');
          
        }else  // END INVITE TO IDEA
          if ($user->invitations > 0){
            $invitee = User::model()->findByAttributes(array("email"=>$invitation->email));
            
            if ($invitee){
              setFlash("invitationMessage",Yii::t('msg','This user is already a member.','alert'));
            }
            else{
              $invitation = Invite::model()->findByAttributes(array('email'=>$_POST['invite-email'],'key'=>null,'registered'=>0)); // self invited from system

              if ($invitation){
                // self invitation exists
                $invitation->sender_id = Yii::app()->user->id;
                $invitation->key = md5(microtime().$invitation->email);
              }else{
                // create invitation
                $invitation = new Invite();
                $invitation->email = $_POST['invite-email'];
                $invitation->sender_id = Yii::app()->user->id;
                $invitation->key = md5(microtime().$invitation->email);
                if (!empty($_POST['invite-idea'])){
                  $invitation->idea_id = $_POST['invite-idea']; // invite to idea
                  //$invitee = User::model()->findByPk(Yii::app()->user->id);
                  //$invitation->id_user = 
                }
              }              
              
              if ($invitation->save()){
                $user->invitations = $user->invitations-1;
                $user->save();

                $stat = UserStat::model()->findByAttributes(array('user_id'=>$user->id));
                $stat->invites_send = $stat->invites_send+1;
                $stat->save();
                
                // incognito tracking (no user in system yet)
                $mailTracking = mailTrackingCode();
                $ml = new MailLog();
                $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
                $ml->type = 'cofinder-invite';
                $ml->user_to_id = null;
                $ml->save();

                //$activation_url = '<a href="'.Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key.'"><strong>Register here</strong></a>';
                $activation_url = mailButton("Register here", Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key, "success", $mailTracking,'register-button');
                $this->sendMail($invitation->email,
                                "You have been invited to join cofinder", 
                                "We've been hard at work on our new service called cofinder.
                                                Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself. 
                                                <br /><br /> <strong>".$user->name." ".$user->surname."</strong> thinks you might be the right person to test our private beta.
                                                <br /><br /> If we got your attention you can ".$activation_url."!",
                                $mailTracking);

              setFlash("invitationMessage",Yii::t('msg','Invitation sent.'));
              //Yii::app()->user->setFlash("invitationMessage",Yii::t('msg','Invitation sent.'));
            }else setFlash("invitationMessage",Yii::t('msg','This user is already invited.'),'alert');
          }
        }
          //Yii::app()->user->setFlash("invitationMessage",Yii::t('msg','This user is already invited.'));

      }
      
      if($this->renderLayout == true){
        $this->render("invite",array("invitations"=>$user->invitations));
      }
      
    }
 
    public function run()
    {
        // this method is called by CController::endWidget()
    }
    
    
    private function sendMail($email, $subject, $content, $tc = ''){
      // send mail
      $message = new YiiMailMessage;
      $message->view = 'system';      
      
      $message->subject = $subject;
      $message->setBody(array("content"=>$content, 'tc' => $tc), 'text/html');
      
      $message->setTo($email);
      $message->from = Yii::app()->params['noreplyEmail'];
      Yii::app()->mail->send($message);
    }
    
}?>



    