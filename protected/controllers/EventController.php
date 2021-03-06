<?php

class EventController extends GxController
{

	public $layout="//layouts/view";
	public $stages = array();

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'signup' actions
        		'actions'=>array("survey"),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'signup' actions
        		'actions'=>array("signup", "suggestReferrer", "view"),
				'users'=>array('@'),
			),
			array('allow', // allow admins only
        		'actions'=>array("show"),  // remove after demo
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id)
	{	

		if(isset($_GET['unsubscribe']) && $_GET['unsubscribe'] == true){
			if($signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0))){
				$signup->canceled = 1;
				$signup->save();
			}
		}

		//list of events user's signed up to
		$filter['user_id'] = Yii::app()->user->id;
		if(Yii::app()->user->isAdmin()){
			$filter['admin_event_id'] = $id;
		}
		$filter['all_events'] = true;
		$events = new SqlBuilder;
		$events = $events->events($filter);

		//list of events user's signed up to in stages module
		//if($filter['user_id'] > 0 && isset($events[$id]) && count($events[$id]) > 0){
			$this->render('index', array('event'=>$events[$id]));
		/*} else {
			$this->redirect(Yii::app()->createUrl('site/startupEvents'));
		}*/
	}

  
	public function actionSurvey($id)
	{	
    $code = 'IOT';
    if (!Yii::app()->user->isGuest){
      $hashids = new Hashids('cofinder');
      $code = $hashids->encrypt(Yii::app()->user->id);
    }
    
    if ($id == 111)	$this->redirect("https://docs.google.com/forms/d/1_efwhCPl7pogAkcjhkBENmvStbxxPOC5Djwyn15GN_U/viewform?entry.600929608=".$code."&entry.1465877792&entry.778104975&entry.316917890&entry.318542958&entry.414414609&entry.718833714");
	}  
  
	public function actionSignup($id, $step = 1)
	{
		//lepagesta implementation
        if(isset($_GET['tag'])){
    		$_SESSION['event_idea_tag'] = $_GET['tag'];
    	}

		if(Yii::app()->user->isGuest){
			$this->redirect(Yii::app()->createUrl('user/registration',array("event"=>$id)));
		} else {
			//lepagesta implementation
			if(isset($_SESSION['event_idea_tag']) && Yii::app()->user->id > 0){
				//save tag to user_tag, for ideas coming from outside
				$user_tag = UserTag::Model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'tag' => $_SESSION['event_idea_tag']));
				if(!$user_tag){
					$user_tag = new UserTag;
					$user_tag->user_id = Yii::app()->user->id;
					$user_tag->tag = $_SESSION['event_idea_tag'];

					//save
					$user_tag->save();
				}
			}
		}

	    switch($step){
            case 1:
                //1. korak - event questions
                $this->actionSignupStep1($id);
                break;
            case 2:
                //2. korak - paypal
                $this->actionSignupStep2($id);
                break;
            case 3:
                //3. korak - finished
                $this->actionSignupStep3($id);
                break;
        }
	}

	private function actionSignupStep1($id){

		$this->layout="//layouts/stageflow";

		$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
		if($event && ($event->price_idea > 0 || $event->price_person > 0)){
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Payment'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		} else {
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		}

		$_SESSION['event'] = $id;

		//save data
		if(isset($_POST['Event'])){

			if($_POST['Event']['present'] == 0 || ($_POST['Event']['present'] == 1 && isset($_POST['Event']['project']) && $_POST['Event']['project'] > 0)){

				//prepare to save signup
				$signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));
				if(!$signup){
					$signup = new EventSignup;
          $new = true;
				}
				$signup->user_id = Yii::app()->user->id;
				$signup->event_id = $id;
				if(isset($_POST['Event']['project']) && $_POST['Event']['present'] == 1){
					$signup->idea_id = $_POST['Event']['project'];
				} else {
					$signup->idea_id = NULL;
				}

				//prepare to save survey
				if(isset($_POST['Survey'])){
					$survey = serialize($_POST['Survey']);
					$signup->survey = $survey;
				}

				//save
				if($signup->save()){
          if ($new) $this->afterRegistrationEmail($id);
					//redirect...
					$comp = new Completeness();
			      	$perc = $comp->getPercentage();

			      	if($perc < PROFILE_COMPLETENESS_MIN){
			      		$user = UserEdit::Model()->findByAttributes(array('id' => Yii::app()->user->id));
			      		$email = $user->email;
			      		$key = substr($user->activkey, 0, 10);

			      		$this->redirect(Yii::app()->createUrl('profile/registrationFlow',array("key"=>$key,"email"=>$email)));
			      	} else {
			      		$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)));
			      	}
			    } else {
			    	setFlash('eventSignupAlert', Yii::t('msg',"Error."), 'alert');
			    }

		    } else {
		    	setFlash('eventSignupAlert', Yii::t('msg',"Please select or create a project."), 'alert');
		    }
			
	    }

	    //is this edit?
	    $signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));
	    if($signup && !isset($_POST['Event'])){
		    if($signup->idea_id == 0){
		    	$_POST['Event']['present'] = 0;
		    } else {
		    	$_POST['Event']['present'] = 1;
		    	$_POST['Event']['project'] = $signup->idea_id;
		    }

		    if(strlen($signup->survey) > 0){
		    	$_POST['Survey'] = unserialize($signup->survey);
		    }
	    }

	    //event title
	    $event = Event::Model()->findByAttributes(array('id' => $id));
	    $title = $event->title;

		//build idea list
		$filter['user_id'] = Yii::app()->user->id;
		$sqlbuilder = new SqlBuilder;
		$data['user'] = $sqlbuilder->load_array("user", $filter);

		//is this intern event?
		$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
		$survey_id = 0;
		if($event){

			//does survey exist?
			$pathFileName = Yii::app()->params['surveyFolder']."_survey".$id.".php";
        	if (file_exists($pathFileName)){
        		$survey_id = $id;
        	}
		}

    	//include survey in view
    	$this->render('signupStep1', array('ideas'=>$data['user']['idea'], 'surveyid' => $survey_id, 'title' => $title));
	}

	private function actionSignupStep2($id){

		$this->layout="//layouts/stageflow";

		$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
		if($event && ($event->price_idea > 0 || $event->price_person > 0)){
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Payment'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		} else {
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		}

		//paypal integration
		$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
		$signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));
    $new = false;
		if(isset($_GET['tx']) && $event && $signup){
			if($return_array = $this->validatePayment($id)){
				$payment_success = false;

				if($signup->idea_id > 0){
					if($return_array['payment_gross'] == $event->price_idea) $payment_success = true;
				} else {
					if($return_array['payment_gross'] == $event->price_person) $payment_success = true;
				}

				if($payment_success == true){
					$signup->payment = $return_array['payment_gross'];
					$signup->save();
				}
			}
		}

		//flow
		$signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));
		if($signup){
			//user has signed up
			$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
			if($event){
				//this is an internal cofinder event
				if($signup->idea_id > 0){
					//user has registered a project for the event
					if($event->price_idea == $signup->payment){
						//the event is either free or user has already paid the amount
						$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3)));
					} else {
						$payment = $event->price_idea;
					}
				} else {
					//user has registered to meet a team
					if($event->price_person == $signup->payment){
						//the event is either free or user has already paid the amount
						$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3)));
					} else {
						$payment = $event->price_person;
					}
				}

				//now we know how much the user has to pay

				//event title
			    $event = Event::Model()->findByAttributes(array('id' => $id));
			    $title = $event->title;

				$this->render('signupStep2', array('id'=>$id, 'title'=>$title, 'payment'=>$payment, 'user_id'=>Yii::app()->user->id));

			} else {
				//this is an external event, therefore we redirect the user to step 3
				$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3)));
			}
		} else {
			$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)));
		}
		
	}

	private function actionSignupStep3($id){

		$this->layout="//layouts/stageflow";

		$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
		if($event && ($event->price_idea > 0 || $event->price_person > 0)){
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Payment'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		} else {
		    $this->stages = array(
		        array('title'=>Yii::t('app','Event survey'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)),'required'=>true),
		        array('title'=>Yii::t('app','Finished'),'url'=>Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>3))),
		    );
		}

		//flow
		$signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));
		if($signup){
			$payment = false;
			//user has signed up
			$event = EventCofinder::Model()->findByAttributes(array('event_id' => $id));
			if($event){
				//this is an internal cofinder event
				if($signup->idea_id > 0){
					//user has registered a project for the event
					if($event->price_idea > $signup->payment){
						//the event is not free and the user hasn't paid yet
						//$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)));
					} else { $payment = true; }
				} else {
					//user has registered to meet a team
					if($event->price_person > $signup->payment){
						//the event is not free and the user hasn't paid yet
						//$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>2)));
					} else { $payment = true; }
				}
			}

			//this is either an external event, or the user has paid

			//send confirmation emails
			$this->afterRegistrationEmail($id);

			//event title
		    $event = Event::Model()->findByAttributes(array('id' => $id));
		    $title = $event->title;

			$this->render('signupStep3', array('id'=>$id, 'title'=>$title, 'payment' => $payment));
		} else {
			$this->redirect(Yii::app()->createUrl('event/signup',array("id"=>$id,"step"=>1)));
		}

	}

	private function validatePayment($id){

		$user = UserEdit::Model()->findByAttributes(array('id' => Yii::app()->user->id));
		$event = Event::Model()->findByAttributes(array('id' => $id));
		$event_extended = Event::Model()->findByAttributes(array('event_id' => $id));

		// read the post from PayPal system and add 'cmd'
	  $req = 'cmd=_notify-synch';
	 
	  $tx_token = $_GET['tx'];
	  $auth_token = "lTQqpUHOsXyc-8IMzFwohLvmN8cyUw0cARqnfMB64pxuz91iEIny8WbyHVS";
	  $req .= "&tx=$tx_token&at=$auth_token";
	 
	  // post back to PayPal system to validate
	  $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	  $header .= "Host: http://www.sandbox.paypal.com\r\n";
	  //$header .= "Host: http://www.paypal.com\r\n";
	  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	  $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	 
	  // url for paypal sandbox
	  $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);   
	 
	  // url for payal
	  // $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
	  // If possible, securely post back to paypal using HTTPS
	  // Your PHP server will need to be SSL enabled
	  // $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	 
	  if (!$fp) {
	    // HTTP ERROR
	  } else {
	    fputs ($fp, $header . $req);
	    // read the body data
	    $res = '';
	    $headerdone = false;
	    while (!feof($fp)) {
	      $line = fgets ($fp, 1024);
	      if (strcmp($line, "\r\n") == 0) {
	        // read the header
	        $headerdone = true;
	      }
	      else if ($headerdone) {
	        // header has been read. now read the contents
	        $res .= $line;
	      }
	    }
	 
	    // parse the data
	    $lines = explode("\n", $res);
	    $keyarray = array();
	    if (strcmp ($lines[0], "SUCCESS") == 0) {
	      for ($i=1; $i<count($lines);$i++){
	        list($key,$val) = explode("=", $lines[$i]);
	        $keyarray[urldecode($key)] = urldecode($val);
	      }
	      // check the payment_status is Completed
	      // check that txn_id has not been previously processed
	      // check that receiver_email is your Primary PayPal email
	      // check that payment_amount/payment_currency are correct
	      // process payment
	 	
	      return($keyarray);
	    }
	    else if (strcmp ($lines[0], "FAIL") == 0) {
	      // log for manual investigation

			// nam sporočilo o failu
/*			$message = new YiiMailMessage;
			$message->view = 'system';
			$message->subject = "Event payment went wrong";
			$message->setBody(array("content"=>'Uporabnik '.$user->name." ".$user->surname.' je poskusil plačati prijavnino na dogodek (ID = '.$id.')<br />'.$res
			), 'text/html');
			                        
			$message->setTo("team@cofinder.eu");
			$message->from = Yii::app()->params['noreplyEmail'];
			Yii::app()->mail->send($message);
*/
	    	return false;
	    }
	  }
	  fclose ($fp);
	}

	private function afterRegistrationEmail($id){
		$user = UserEdit::Model()->findByAttributes(array('id' => Yii::app()->user->id));
		$event = Event::Model()->findByAttributes(array('id' => $id));
		$signup = EventSignup::Model()->findByAttributes(array('event_id' => $id, 'user_id' => Yii::app()->user->id, 'canceled' => 0));

		if(isset($signup->idea_id) && $signup->idea_id > 0){
			$text = "predstavil idejo ".Yii::app()->createAbsoluteUrl('project',array("id"=>$signup->idea_id));
		} else {
			$text = "pridružil se zanimivemu projektu";
		}

		// nam sporočilo o registraciji z mailom
		$message = new YiiMailMessage;
		$message->view = 'system';
		$message->subject = "Dogodek: " .$event->title. " (".$user->name." ".$user->surname.")";
		$message->setBody(array("content"=>'Uporabnik '.$user->name." ".$user->surname.' se je pravkar prijavil na dogodek.<br />
		Njegov email: '.$user->email.'<br />'.
		'Rad bi '.$text.'.<br />'
		), 'text/html');
		                        
		$message->setTo("team@cofinder.eu");
    if ($id == (111 || 253)) $message->setTo("cofinder@hekovnik.com");
		$message->from = Yii::app()->params['noreplyEmail'];
		Yii::app()->mail->send($message);

		// njemu sporočilo o uspešni registraciji in plačilu (opcijsko)
		$message = new YiiMailMessage;
		$message->view = 'system';
		$message->subject = "Dogodek ".$event->title. ": prijava je bila uspešna";
		$message->setBody(array("content"=>'Pozdravljeni!<br /><br />Uspešno ste se prijavili na dogodek '.$event->title.'.<br />
		Hvala za prijavo, se vidimo na dogodku!<br /><br />Lp, ekipa Cofinder'
		), 'text/html');
		                        
		$message->setTo($user->email);
		$message->from = Yii::app()->params['noreplyEmail'];
		Yii::app()->mail->send($message);
	}

	public function actionSuggestReferrer($term){
	    
	    $dataReader = User::model()->findAll("(name LIKE :name OR surname LIKE :name) AND status = 1", array(":name"=>"%".$term."%"));

	    if ($dataReader){
	      foreach ($dataReader as $row){
	        $avatar = avatar_image($row->avatar_link,$row->id,60);
	        $data[] = array("fullname"=>$row->name." ".$row->surname,
	                        "user_id"=>$row->id,
	                        //"img"=>avatar_image($row->avatar_link,$row->id),
	                        "img"=>$avatar,
	                        );
	      }
	    }
	    
	    $response = array("data" => $data,
													"status" => 0,
													"message" => '');
			
		echo json_encode($response);
		Yii::app()->end();
	}

}
