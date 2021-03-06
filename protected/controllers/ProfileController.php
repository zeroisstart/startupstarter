<?php

class ProfileController extends GxController {

  public $layout="//layouts/edit";
  public $stages = array();

	/**
	 * @return array action filters
	 */
	public function filters() {
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
	public function accessRules() {
		return array(
				array('allow',
						'actions' => array('registrationFlow','addSkill','deleteSkill','suggestSkill','upload'),
						/*'users' => array("?"),*/
            'expression' => array($this,'isLogedInOrAfterRegister'),
				),
				array('allow',
						'actions' => array('index', 'view', 'projects', 'account','upload','removeIdea','addIdea', 
                               'addLink','deleteLink','addSkill','deleteSkill','suggestSkill',
                               'notification','acceptInvitation','completeness'),
						'users' => array("@"),
				),
				array('allow', // allow admins only
						'users' => Yii::app()->getModule('user')->getAdmins(),
				),
				array('deny', // deny all users
						'users' => array('*'),
				),
		);
	}
  
  function isLogedInOrAfterRegister($user, $rule){
    return true;
    if (Yii::app()->user->isGuest && isset($_GET['key']) && isset($_GET['email']) && !empty($_GET['key']) && !empty($_GET['email'])){
      $user_register = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));    
      if (!$user_register || ((substr($user_register->activkey, 0, 10) !== $_GET['key']) || ($user_register->status != 0))){
        return false;
      }
      return true;
    }else return true;
  }

	public function actionUpload() {
		Yii::import("ext.EAjaxUpload.qqFileUploader");

		$folder = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . Yii::app()->params['tempFolder']; // folder for uploaded files

		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
			//mkdir( $folder );
			//chmod( $folder, 0777 );
		}

		$allowedExtensions = array("jpg", "jpeg", "png"); //array("jpg","jpeg","gif","exe","mov" and etc...
		$sizeLimit = 10 * 1024 * 1024; // maximum file size in bytes
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($folder);
		$return = json_encode($result);

		$fileSize = filesize($folder . $result['filename']); //GETTING FILE SIZE
		$fileName = $result['filename']; //GETTING FILE NAME

		if (true) {
			//throw new Exception(print_r($result,true));
			Yii::import("ext.EPhpThumb.*");
			$thumb = new EPhpThumb();
			$thumb->init(); //this is needed
      $thumb->create($folder . $fileName)
            ->adaptiveResize(250, 250)
            ->save($folder . $fileName);
		}

		echo $return; // it's array
	}
  
  
  
  private function saveSettings($user_id){


			$user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
			if ($user) {
				$oldImg = $user->avatar_link;
				$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
        
        // VANITY URL
        $us = UserStat::model()->findByAttributes(array("user_id"=>$user_id));
        // set only if has invited at least 3 other people
        $allowVanityURL = ($us && (/*($user->vanityURL != '') || */($us->invites_send > 2)));

        
        //user skills
        if (isset($_POST['hidden-skill'])) CSkills::saveSkills($_POST['hidden-skill'],$match->id);

        // user model
				if (isset($_POST['UserEdit'])) {
         
          //VANITY URL
          if (isset($_POST['UserEdit']['vanityURL'])){
            if (!$allowVanityURL) $_POST['UserEdit']['vanityURL'] = $user->vanityURL;
            else{
              if ($_POST['UserEdit']['vanityURL'] != null){
                if (strpos($_POST['UserEdit']['vanityURL'],'.') !== false) $user->addError('vanityURL', Yii::t('msg','Dots "." are not allowed in public name.'));
                // check validity of vanity URL in projects
                if ($_POST['UserEdit']['vanityURL'] != $user->vanityURL){
                  $ideaURL = Idea::model()->findByAttributes(array('vanityURL'=>$_POST['UserEdit']['vanityURL']));
                  if ($ideaURL){
                    //echo "b";
                    $user->addError('vanityURL', Yii::t('msg',"This custom URL already exists."));
                  }
                }
              }
            }
          }// end vanity url check
          
          
					$user->setAttributes($_POST['UserEdit']);
					//$user->avatar_link = '';

					if (isset($_POST['UserEdit']['avatar_link'])) {
						$filename = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['tempFolder'] . $_POST['UserEdit']['avatar_link'];

						// if we need to create avatar image
						if (is_file($filename)) {
							$newFilePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['avatarFolder'];
							//$newFilePath = Yii::app()->params['avatarFolder'];
							if (!is_dir($newFilePath)) {
								mkdir($newFilePath, 0777, true);
								//chmod( $newFilePath, 0777 );
							}
							$newFileName = str_replace(".", "", microtime(true)) . "." . pathinfo($filename, PATHINFO_EXTENSION);

							if (rename($filename, $newFilePath . $newFileName)) {
								// make a thumbnail for avatar
								Yii::import("ext.EPhpThumb.EPhpThumb");
								$thumb = new EPhpThumb();
								$thumb->init(); //this is needed
								$thumb->create($newFilePath . $newFileName)
												->resize(30, 30)
												->save($newFilePath . "thumb_30_" . $newFileName);
								$thumb->create($newFilePath . $newFileName)
												->resize(60, 60)
												->save($newFilePath . "thumb_60_" . $newFileName);

								// save avatar link
								$user->avatar_link = $newFileName;

								// remove old avatar
								if ($oldImg && ($oldImg != $newFileName)) {
									@unlink($newFilePath . $oldImg);
									@unlink($newFilePath . "thumb_30_" . $oldImg);
									@unlink($newFilePath . "thumb_60_" . $oldImg);
								}
							}else
								$user->avatar_link = '';
						}
					}// end post check 

          if (!$user->hasErrors()){
    				$user->validate();
  					$match->validate();
          }
          
					if (!$user->hasErrors() && $user->save()) {
						$_POST['UserMatch']['user_id'] = $user_id;
            
            $c = new Completeness();
            $c->setPercentage($user_id);
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
					}
				}
        
        // user match save
        if (isset($_POST['UserMatch'])) {
          $match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
          $match_id = $match->id;
          $match->setAttributes($_POST['UserMatch']);
          
          if (!empty($_POST['UserMatch']['city'])){
            $city = City::model()->findByAttributes(array('name'=>$_POST['UserMatch']['city']));
            if ($city) $match->city_id = $city->id;
            else{
              $city = new City();
              $city->name = $_POST['UserMatch']['city'];
              $city->save();
              $match->city_id = $city->id;
            }
          }else if (isset($_POST['UserMatch']['city'])) $match->city_id = null;
          
          $c = 0;
          if (isset($_POST['CollabPref'])){
            UserCollabpref::Model()->deleteAll("match_id = :match_id", array(':match_id' => $match_id));
            $c = count($_POST['CollabPref']);
            foreach ($_POST['CollabPref'] as $collab => $collab_name){
              $user_collabpref = new UserCollabpref;
              $user_collabpref->match_id = $match_id;
              $user_collabpref->collab_id = $collab;
              if ($user_collabpref->save()) $c--;
            }
          }
          
          if (($c == 0) && ($match->save())) {
            //if (Yii::app()->user->isGuest) 
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            //else setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            $c = new Completeness();
            $c->setPercentage($user_id);
          }else{
            setFlash('profileMessage', Yii::t('msg',"Unable to save profile details."),'alert');
          }
          
        }

        $link = new UserLink;
				$filter['user_id'] = $user_id;
				$sqlbuilder = new SqlBuilder;
        
				if (Yii::app()->user->isGuest){
          
          
          $this->stages = array(
            array('title'=>Yii::t('app','Profile'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>1))),
            array('title'=>Yii::t('app','Skills & preferences'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>2))),
            array('title'=>Yii::t('app','About me'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>3))),
          );
          
          $c = new Completeness();
          $perc = $c->getPercentage($user_id);
          
          $this->layout="//layouts/stageflow";
          $data['user'] = $sqlbuilder->load_array("regflow", $filter);
          if (isset($_GET['step'])){
            $this->render('registrationFlow_'.$_GET['step'], array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link,'perc'=>$perc));
          }else $this->render('registrationFlow_1', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link,'perc'=>$perc));
        }
        else {
          $data['user'] = $sqlbuilder->load_array("user", $filter, "collabpref,link,idea,member,skill,industry");
          $this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea'], "allowVanityURL"=>$allowVanityURL));
        }

        //if (Yii::app()->user->isGuest) $this->render('registrationFlow', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link));
        //else $this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea']));
			}    
  }
  
  
  /**
   * 
   */
	public function actionIndex() {

		/*echo 'Links: <br/><br/>

		Views<br/>
		/ -> edit user profile<br/>
		/projects/ -> edit users projects<br/>
		/account/ -> edit account settings<br/>
		for admin-can-edit-anything purposes... add ?user_id=$user_id to the above 3 views<br/>
		
		<br/><br/>

		Data manipulation actions<br/>
		/removeIdea/$match_id&idea_id=$idea_id -> remove idea by match_id and idea_id <br/>
		/addCollabpref/$match_id -> add collabpref by match_id<br/>
		/deleteCollabpref/$match_id&collab_id=$collab_id -> delete collabpref by match_id and collab_id from user_collabpref<br/>
		/addSkill/$match_id -> add skill by match_id<br/>
		/deleteSkill/$match_id&skill_id=$skill_id -> delete skill by match_id and userskill_id<br/>
		/addLink/$user_id -> add link by user_id<br/>
		/deleteLink/$user_id?link_id=$link_id -> delete link by user_id, link_id<br/>
		<br/>';*/

    
    if (Yii::app()->user->isGuest && isset($_GET['key']) && isset($_GET['email']) && !empty($_GET['key']) && !empty($_GET['email'])){
      $user_register = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));    
      if ((substr($user_register->activkey, 0, 10) !== $_GET['key']) || ($user_register->status != 0)){
        $this->render('/site/message',array('title'=>Yii::t('app','Registration finished'),"content"=>Yii::t('msg','Thank you for your registration.')));
        return;
      }
      $user_id = $user_register->id;
    }else $user_id = Yii::app()->user->id;
    
    

		if ($user_id > 0) {

			$user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
			if ($user) {
				$oldImg = $user->avatar_link;
				$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
        
        // VANITY URL
        $us = UserStat::model()->findByAttributes(array("user_id"=>$user_id));
        // set only if has invited at least 3 other people
        $allowVanityURL = ($us && (/*($user->vanityURL != '') || */($us->invites_send > 2)));

        
        //user skills
        if (isset($_POST['hidden-skill'])) CSkills::saveSkills($_POST['hidden-skill'],$match->id);

        // user model
				if (isset($_POST['UserEdit'])) {
         
          //VANITY URL
          if (isset($_POST['UserEdit']['vanityURL'])){
            if (!$allowVanityURL) $_POST['UserEdit']['vanityURL'] = $user->vanityURL;
            else{
              if ($_POST['UserEdit']['vanityURL'] != null){
                if (strpos($_POST['UserEdit']['vanityURL'],'.') !== false) $user->addError('vanityURL', Yii::t('msg','Dots "." are not allowed in public name.'));
                // check validity of vanity URL in projects
                if ($_POST['UserEdit']['vanityURL'] != $user->vanityURL){
                  $ideaURL = Idea::model()->findByAttributes(array('vanityURL'=>$_POST['UserEdit']['vanityURL']));
                  if ($ideaURL){
                    //echo "b";
                    $user->addError('vanityURL', Yii::t('msg',"This custom URL already exists."));
                  }
                }
              }
            }
          }// end vanity url check
          
          
					$user->setAttributes($_POST['UserEdit']);
					//$user->avatar_link = '';

					if (isset($_POST['UserEdit']['avatar_link'])) {
						$filename = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['tempFolder'] . $_POST['UserEdit']['avatar_link'];

						// if we need to create avatar image
						if (is_file($filename)) {
							$newFilePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['avatarFolder'];
							//$newFilePath = Yii::app()->params['avatarFolder'];
							if (!is_dir($newFilePath)) {
								mkdir($newFilePath, 0777, true);
								//chmod( $newFilePath, 0777 );
							}
							$newFileName = str_replace(".", "", microtime(true)) . "." . pathinfo($filename, PATHINFO_EXTENSION);

							if (rename($filename, $newFilePath . $newFileName)) {
								// make a thumbnail for avatar
								Yii::import("ext.EPhpThumb.EPhpThumb");
								$thumb = new EPhpThumb();
								$thumb->init(); //this is needed
								$thumb->create($newFilePath . $newFileName)
												->resize(30, 30)
												->save($newFilePath . "thumb_30_" . $newFileName);
								$thumb->create($newFilePath . $newFileName)
												->resize(60, 60)
												->save($newFilePath . "thumb_60_" . $newFileName);

								// save avatar link
								$user->avatar_link = $newFileName;

								// remove old avatar
								if ($oldImg && ($oldImg != $newFileName)) {
									@unlink($newFilePath . $oldImg);
									@unlink($newFilePath . "thumb_30_" . $oldImg);
									@unlink($newFilePath . "thumb_60_" . $oldImg);
								}
							}else
								$user->avatar_link = '';
						}
					}// end post check 

          if (!$user->hasErrors()){
    				$user->validate();
  					$match->validate();
          }
          
					if (!$user->hasErrors() && $user->save()) {
						$_POST['UserMatch']['user_id'] = $user_id;
            
            $c = new Completeness();
            $c->setPercentage($user_id);
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
					}
				}
        
        // user match save
        if (isset($_POST['UserMatch'])) {
          $match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
          $match_id = $match->id;
          $match->setAttributes($_POST['UserMatch']);
          
          if (!empty($_POST['UserMatch']['city'])){
            $city = City::model()->findByAttributes(array('name'=>$_POST['UserMatch']['city']));
            if ($city) $match->city_id = $city->id;
            else{
              $city = new City();
              $city->name = $_POST['UserMatch']['city'];
              $city->save();
              $match->city_id = $city->id;
            }
          }else if (isset($_POST['UserMatch']['city'])) $match->city_id = null;
          
          $c = 0;
          if (isset($_POST['CollabPref'])){
            UserCollabpref::Model()->deleteAll("match_id = :match_id", array(':match_id' => $match_id));
            $c = count($_POST['CollabPref']);
            foreach ($_POST['CollabPref'] as $collab => $collab_name){
              $user_collabpref = new UserCollabpref;
              $user_collabpref->match_id = $match_id;
              $user_collabpref->collab_id = $collab;
              if ($user_collabpref->save()) $c--;
            }
          }
          
          if (($c == 0) && ($match->save())) {
            //if (Yii::app()->user->isGuest) 
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            //else setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            $c = new Completeness();
            $c->setPercentage($user_id);
          }else{
            setFlash('profileMessage', Yii::t('msg',"Unable to save profile details."),'alert');
          }
          
        }

        $link = new UserLink;
				$filter['user_id'] = $user_id;
				$sqlbuilder = new SqlBuilder;
        
        $data['user'] = $sqlbuilder->load_array("user", $filter, "collabpref,link,idea,member,skill,industry");
        $this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea'], "allowVanityURL"=>$allowVanityURL));

        //if (Yii::app()->user->isGuest) $this->render('registrationFlow', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link));
        //else $this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea']));
			}
		}
	}

  
  /**
   * 
   */
	public function actionProjects() {

		$user_id = Yii::app()->user->id;

		if ($user_id > 0) {

            //publish project
            if(!empty($_GET['publish']) and !empty($_GET['id']))
            {
                $idea = Idea::Model()->findByAttributes(array('id' => $_GET['id']));
                $match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
                $ideaMember = IdeaMember::Model()->findByAttributes(array('type_id' => 1,'match_id' => $match->id, 'idea_id' => $_GET['id']));

                if($idea && $ideaMember){
                    $idea->deleted = 0;
                    $idea->save();
                }
            }

            //projects sql builder
			$filter['user_id'] = $user_id;
			$sqlbuilder = new SqlBuilder;
			$user = $sqlbuilder->load_array("user", $filter, "collabpref,link,idea,member");

			$this->render('projects', array('user' => $user, "ideas"=>$user['idea']));
		} else {
			$this->redirect(array('profile/'));
		}
	}

  
  /**
   * 
   */
	public function actionAccount() {

		//email
		//password
		//password confirm
		//these are for later
		//language
		//newsletter
		//check for permission

		$user_id = Yii::app()->user->id;
		$user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
		//$fpi = !Yii::app()->user->getState('fpi'); // sinc it is not defined default value is 0 and it must be visible

    $us = UserStat::model()->findByAttributes(array("user_id"=>$user_id));
        // set only if has invited at least 3 other people
    $allowVanityURL = ($us && (/*($user->vanityURL != '') || */($us->invites_send > 2)));
        
		if ($user) {

			if (isset($_POST['UserEdit'])) {
				//$_POST['UserEdit']['name'] = $user->name;
				//$fpi = $_POST['UserEdit']['fpi'];
				//Yii::app()->user->setState('fpi', !$fpi);

				//unset($_POST['UserEdit']['fpi']); // since we don't have it in our user model
				$_POST['UserEdit']['email'] = $user->email; // can't change email at this time!!!
        
        if (!$allowVanityURL) $_POST['UserEdit']['vanityURL'] = $user->vanityURL;
        else{
          if ($_POST['UserEdit']['vanityURL'] != null){
            if (strpos($_POST['UserEdit']['vanityURL'],'.') !== false) $user->addError('vanityURL', Yii::t('msg','Dots "." are not allowed in public name.'));
            // check validity of vanity URL in projects
            if ($_POST['UserEdit']['vanityURL'] != $user->vanityURL){
              $ideaURL = Idea::model()->findByAttributes(array('vanityURL'=>$_POST['UserEdit']['vanityURL']));
              if ($ideaURL){
                //echo "b";
                $user->addError('vanityURL', Yii::t('msg',"This custom URL already exists."));
              }
            }
          }
        }

				$user->setAttributes($_POST['UserEdit']);
        
        if (isset($_POST['UserEdit']['qrcodepair']) && ($_POST['UserEdit']['qrcodepair'] == 0)){
          $user->qrcode = null;
        }
        
				if (!$user->hasErrors() && $user->save()) {
					if ($user->language_id !== null) {
						$lang = Language::Model()->findByAttributes(array('id' => $user->language_id));
						ELangPick::setLanguage($lang->language_code);
					}
          $c = new Completeness();
          $c->setPercentage($user_id);

					/* if (Yii::app()->getRequest()->getIsAjaxRequest())
					  Yii::app()->end();
					  else{ */
					setFlash('settingsMessage', Yii::t('msg',"Settings saved."));
					//$this->redirect(array('profile/account/'));
					//}
				}
			} else
			if (isset($_POST['deactivate_account']) && ($_POST['deactivate_account'] == 1)) {
                Slack::message("USER >> Account deactivation: ".$user->email);
				$user->status = 0;
				if ($user->save())
					$this->redirect(array('user/logout'));
			}

			// password changing
			$form2 = new UserChangePassword;
			$find = User::model()->findByPk(Yii::app()->user->id);
			if (isset($_POST['UserChangePassword'])) {
				$form2->attributes = $_POST['UserChangePassword'];
				if ($form2->validate()) {
					$find->password = UserModule::createHash($form2->password);
					$find->activkey = UserModule::encrypting(microtime() . $form2->password);
					if ($find->status == 0) {
						$find->status = 1;
					}
					$find->save();
					setFlash('passChangeMessage', Yii::t('msg',"New password is saved."));
					//$this->redirect(Yii::app()->controller->module->recoveryUrl);
				}
			}

			$filter['user_id'] = $user_id;
			$sqlbuilder = new SqlBuilder;
			$data['user'] = $sqlbuilder->load_array("user", $filter);
			//$this->ideas = $data['user']['idea'];

			$this->render('account', array('user' => $user, "passwordForm" => $form2, /*"fpi" => $fpi,*/ 'ideas'=>$data['user']['idea'],'allowVanityURL'=>$allowVanityURL));
		}
	}

	//from here on there's only ajax actions
	public function actionRemoveIdea($id) {

		$user_id = Yii::app()->user->id;

		$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
		$match_id = $match->id;

		$isOwner = IdeaMember::Model()->findByAttributes(array('match_id' => $match_id, 'idea_id' => $id, 'type_id' => 1));

		//check for permission
		if ($user_id > 0) {

			if ($isOwner) {
				$idea = Idea::Model()->findByAttributes(array('id' => $id, 'deleted' => 0));
				$idea->setAttributes(array('deleted' => 1));

				if ($idea->save())
					$allgood = true;

			} else {
				$member = IdeaMember::Model()->findByAttributes(array('idea_id' => $id, 'match_id' => $match_id));
				
				if ($member->delete())
					$allgood = true;
			}

			if ($allgood) {
				$return['message'] = Yii::t('msg', "Project successfully removed!");
				$return['status'] = 0;
        $c = new Completeness();
        $c->setPercentage($user_id);
			} else {
				$return['message'] = Yii::t('msg', "Unable to remove project from your account.");
				$return['status'] = 1;
			}

			if (isset($_GET['ajax'])) {
				$return = json_encode($return);
				echo $return; //return array
				Yii::app()->end();
			}
		}
	}

	public function actionCollabpref() {

		$user_id = Yii::app()->user->id;

		$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
		$match_id = $match->id;

		//check for permission
		if ($user_id > 0) {
			$collabpref = new UserCollabpref;

			if (isset($_POST['UserCollabpref'])) {

				foreach ($POST['UserCollabpref'] AS $key => $value) {
					$value['match_id'] = $match_id;
					$allgood = false;

					$exists = UserCollabpref::Model()->findByAttributes(array('match_id' => $match_id, 'collab_id' => $value['collab_id']));
					if (!$exists && $value['active'] > 0) { //then we want to insert
						$collabpref->setAttributes($value);
						if ($collabpref->save())
							$allgood = true;
					}
					if ($exists && !$_POST['UserCollabpref']['active']) { //then we want to delete it
						if ($exists->delete())
							$allgood = true;
					}
				}

				if ($allgood) {
					$return['message'] = Yii::t('msg', "The collaboration preferences were successfully updated.");
					$return['status'] = 0;
          
          $c = new Completeness();
          $c->setPercentage($user_id);
				} else {
					$return['message'] = Yii::t('msg', "Unable to update collaboration preferences.");
					$return['status'] = 1;
				}

				if (isset($_GET['ajax'])) {
					$return = json_encode($return);
					echo $return; //return array
					Yii::app()->end();
				}
			}
		}
	}

	public function actionAddSkill() {

	    if (Yii::app()->user->isGuest && isset($_GET['key']) && isset($_GET['email']) && !empty($_GET['key']) && !empty($_GET['email'])){
	      $user_register = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));
	      
	      	if (!$user_register || ((substr($user_register->activkey, 0, 10) !== $_GET['key']) || ($user_register->status != 0))){
				$return['message'] = Yii::t('msg', "Unable to add skill.");
				$return['status'] = 1;
        		$return = json_encode($return);
				echo $return; //return array
        		return;
	      	}
	      	$user_id = $user_register->id;
	    }else $user_id = Yii::app()->user->id;
    
    
		$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
		$match_id = $match->id;

    	$response = '';
		//check for permission
		if ($user_id > 0) {

			if (!empty($_POST['skill'])) {
		        //$skill = new UserSkill;
		        
		        $skillsExtractor = new Keyworder;
		        $skills = $skillsExtractor->string2array($_POST['skill']);

		        foreach ($skills as $row){
		          if ($row == '') continue; // if empty
		          
		          $skill = new Skill;
		          $skill->name = $row;
		          if (!$skill->save()) $skill = Skill::model()->findByAttributes(array("name"=>$row));



		          $user_skill = UserSkill::model()->findByAttributes(array("skill_id"=>$skill->id,
		                                                                  "match_id"=>$match_id,));
		          if ($user_skill == null){
		            $user_skill = new UserSkill;
		            $user_skill->skill_id = $skill->id;
		            $user_skill->match_id = $match_id;

		            if ($user_skill->save()){
		              $c = new Completeness();
		              $c->setPercentage($user_id);

				        //update usage count
				        $count = $skill->count;
				        $count++;
				        $skill->count = $count;
				        $skill->save();

		              $response = array("data" => array("title" => $_POST['skill'],
		                                                "id" => $user_skill->id,
		                                                "location" => Yii::app()->createUrl("profile/deleteSkill"),
		                                                "desc" => $row, 
		                                                "multi" => count($skills),
		                                ),
		              "status" => 0,
		              "message" => Yii::t('msg', "Skill added."));
		            }else{
		              $response = array("data" => null,
		                "status" => 1,
		                "message" => Yii::t('msg', "Problem saving skill. Please check fields for correct values."));
		              break;
		            }
		          }else{
		              $response = array("data" => null,
		                "status" => 1,
		                "message" => Yii::t('msg', "You already have this skill."));
		          }

		        }
		        echo json_encode($response);
		        Yii::app()->end();
        
      		// end set skill
			}else{
				$response = array("data" => null,
						"status" => 1,
						"message" => Yii::t('msg', "Please enter a skill."));

				echo json_encode($response);
				Yii::app()->end();
			}
		}
	}

  /**
   * 
   */
	public function actionDeleteSkill() {
	    if (Yii::app()->user->isGuest && isset($_GET['key']) && isset($_GET['email']) && !empty($_GET['key']) && !empty($_GET['email'])){
	      	$user_register = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));    
	      	if (!$user_register || ((substr($user_register->activkey, 0, 10) !== $_GET['key']) || ($user_register->status != 0))){
            $return['message'] = Yii::t('msg', "Unable to remove skill.");
            $return['status'] = 1;
	        	$return = json_encode($return);
            echo $return; //return array
	        	Yii::app()->end();
	      	}
	      	$user_id = $user_register->id;
	    }else $user_id = Yii::app()->user->id;
	    
	    $skill_id = 0;
		if (isset($_POST['id']))
			$skill_id = $_POST['id'];

		if ($user_id > 0 && $skill_id) {
			$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));

			$skill = UserSkill::Model()->findByAttributes(array('skill_id' => $skill_id,'match_id'=>$match->id));

			if ($skill->delete()) { //delete

				$skill = Skill::Model()->findByAttributes(array('id' => $skill_id));

				//usage count
				$count = $skill->count;
				$count = $count - 1;
				$skill->count = $count;
				$skill->save();

				$return['message'] = '';
				$return['status'] = 0;
        
        $c = new Completeness();
        $c->setPercentage($user_id);        
			} else {
				$return['message'] = Yii::t('msg', "Unable to remove skill.");
				$return['status'] = 1;
			}

			if (isset($_POST['ajax']) || isset($_GET['ajax'])) {
				$return = json_encode($return);
				echo $return; //return array
				Yii::app()->end();
			}
		}
	}

	public function actionSuggestSkill() {

		if (!isset($_GET['term'])){
			$response = array("data" => null,
								"status" => 1,
								"message" => Yii::t('msg', "No search query."));
	    }else{
	      $data = CSkills::skillSuggest($_GET['term']);
	      /*foreach ($dataReader as $row){
	        $data[] = $row;
	      }*/

	      $response = array("data" => $data,
	                "status" => 0,
	                "message" => '');
			}
			
			echo json_encode($response);
			Yii::app()->end();
	}	
	
  /**
   * 
   */
	public function actionAddLink() {

		$user_id = Yii::app()->user->id;

		if ($user_id > 0) {

			$link = new UserLink;

			if (isset($_POST['UserLink'])) {

				$_POST['UserLink']['user_id'] = $user_id;
				$linkURL = $_POST['UserLink']['url'];

				$exists = UserLink::Model()->findByAttributes(array('user_id' => $user_id, 'url' => $linkURL));
				if (!$exists) {

					$link->setAttributes($_POST['UserLink']);
					$link->url = $linkURL;

					if ($link->save()) {
            $c = new Completeness();
            $c->setPercentage($user_id);

						$response = array("data" => array("title" => $_POST['UserLink']['title'],
										"url" => $linkURL,
										"id" => $link->id,
										"location" => Yii::app()->createUrl("profile/deleteLink")
								),
								"status" => 0, // a damo console status kjer je 0 OK vse ostale cifre pa error????
								"message" => Yii::t('msg', "Link successfully saved to profile."));
					} else {
						$response = array("data" => null,
								"status" => 1,
								"message" => Yii::t('msg', "Problem saving link. Please check fields for correct values."));
					}
				} else {
					$response = array("data" => null,
							"status" => 1,
							"message" => Yii::t('msg', "You already have this link."));
				}

				echo json_encode($response);
				Yii::app()->end();
			}
		}
	}

	public function actionDeleteLink() {

		$user_id = Yii::app()->user->id;
		$link_id = 0;
		if (isset($_POST['id']))
			$link_id = $_POST['id'];

		if ($user_id > 0 && $link_id) {

			$link = UserLink::Model()->findByAttributes(array('id' => $link_id,'user_id' => $user_id));

			if ($link->delete()) {
        $c = new Completeness();
        $c->setPercentage($user_id);
        
				$response = array("data" => array("id" => $link_id),
						"status" => 0,
						"message" => "Link successfully removed from profile.");
			} else {
				$response = array("data" => null,
						"status" => 1,
						"message" =>  Yii::t('msg', "Unable to remove link."));
			}

			echo json_encode($response);
			Yii::app()->end();
		}
	}
  
  public function actionRegistrationFlow(){
    
    $this->layout="//layouts/card";
    
    //if (!Yii::app()->user->isGuest && isset($_get['event'])) $this->redirect(array('event/'));
    
    if (!isset($_GET['key']) || !isset($_GET['email']) || empty($_GET['key']) || empty($_GET['email'])){
      $this->render('/site/message',array('title'=>Yii::t('app','Registration finished'),"content"=>Yii::t('msg','Thank you for your registration.')));
      return;
    }
    
    if (Yii::app()->user->isGuest && isset($_GET['key']) && isset($_GET['email']) && !empty($_GET['key']) && !empty($_GET['email'])){
      $user_register = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));    
      if ((substr($user_register->activkey, 0, 10) !== $_GET['key']) /*|| ($user_register->status != 0)*/){
        $this->render('/site/message',array('title'=>Yii::t('app','Registration finished'),"content"=>Yii::t('msg','Thank you for your registration.')));
        return;
      }
      $user_id = $user_register->id;
    }else $user_id = Yii::app()->user->id;
    

	  $user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
      
     /* 
//      $this->redirect(Yii::app()->createUrl("user/login"));
    $user = User::model()->notsafe()->findByAttributes(array('email'=>$_GET['email']));
    if ((substr($user->activkey, 0, 10) !== $_GET['key'])
        /*|| ($user->status != 0)* /){
      $this->render('/site/message',array('title'=>Yii::t('app','Registration finished'),"content"=>Yii::t('msg','Thank you for your registration.')));
      return;
    }*/
    
    if ($user){
    //mark user but not as member yet
     //Yii::import('application.helpers.Hashids');
     $hashids = new Hashids('cofinder');
     $uid = $hashids->encrypt($user->id);
     
     $baseUrl = Yii::app()->baseUrl; 
     $cs = Yii::app()->getClientScript();
     $cs->registerScript("ganalyticsregister","ga('send', 'event', 'registration', 'mark_user',{'dimension1':'".$uid."',})");      
    }
    

			if ($user) {
				$oldImg = $user->avatar_link;
				$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
        
        // VANITY URL
        $us = UserStat::model()->findByAttributes(array("user_id"=>$user_id));
        // set only if has invited at least 3 other people
        $allowVanityURL = ($us && (/*($user->vanityURL != '') || */($us->invites_send > 2)));

        
        //user skills
        if (isset($_POST['hidden-skill'])) CSkills::saveSkills($_POST['hidden-skill'],$match->id);

        // user model
				if (isset($_POST['UserEdit'])) {
         
          //VANITY URL
          if (isset($_POST['UserEdit']['vanityURL'])){
            if (!$allowVanityURL) $_POST['UserEdit']['vanityURL'] = $user->vanityURL;
            else{
              if ($_POST['UserEdit']['vanityURL'] != null){
                if (strpos($_POST['UserEdit']['vanityURL'],'.') !== false) $user->addError('vanityURL', Yii::t('msg','Dots "." are not allowed in public name.'));
                // check validity of vanity URL in projects
                if ($_POST['UserEdit']['vanityURL'] != $user->vanityURL){
                  $ideaURL = Idea::model()->findByAttributes(array('vanityURL'=>$_POST['UserEdit']['vanityURL']));
                  if ($ideaURL){
                    //echo "b";
                    $user->addError('vanityURL', Yii::t('msg',"This custom URL already exists."));
                  }
                }
              }
            }
          }// end vanity url check
          
          
					$user->setAttributes($_POST['UserEdit']);
					//$user->avatar_link = '';

					if (isset($_POST['UserEdit']['avatar_link'])) {
						$filename = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['tempFolder'] . $_POST['UserEdit']['avatar_link'];

						// if we need to create avatar image
						if (is_file($filename)) {
							$newFilePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['avatarFolder'];
							//$newFilePath = Yii::app()->params['avatarFolder'];
							if (!is_dir($newFilePath)) {
								mkdir($newFilePath, 0777, true);
								//chmod( $newFilePath, 0777 );
							}
							$newFileName = str_replace(".", "", microtime(true)) . "." . pathinfo($filename, PATHINFO_EXTENSION);

							if (rename($filename, $newFilePath . $newFileName)) {
								// make a thumbnail for avatar
								Yii::import("ext.EPhpThumb.EPhpThumb");
								$thumb = new EPhpThumb();
								$thumb->init(); //this is needed
								$thumb->create($newFilePath . $newFileName)
												->resize(30, 30)
												->save($newFilePath . "thumb_30_" . $newFileName);
								$thumb->create($newFilePath . $newFileName)
												->resize(60, 60)
												->save($newFilePath . "thumb_60_" . $newFileName);

								// save avatar link
								$user->avatar_link = $newFileName;

								// remove old avatar
								if ($oldImg && ($oldImg != $newFileName)) {
									@unlink($newFilePath . $oldImg);
									@unlink($newFilePath . "thumb_30_" . $oldImg);
									@unlink($newFilePath . "thumb_60_" . $oldImg);
								}
							}else
								$user->avatar_link = '';
						}
					}// end post check 

          if (!$user->hasErrors()){
    				$user->validate();
  					$match->validate();
          }
          
					if (!$user->hasErrors() && $user->save()) {
						$_POST['UserMatch']['user_id'] = $user_id;
            
            $c = new Completeness();
            $c->setPercentage($user_id);
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
					}
				}
        
        // user match save
        if (isset($_POST['UserMatch'])) {
          $match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
          $match_id = $match->id;
          $match->setAttributes($_POST['UserMatch']);
          
          if (!empty($_POST['UserMatch']['city'])){
            $city = City::model()->findByAttributes(array('name'=>$_POST['UserMatch']['city']));
            if ($city) $match->city_id = $city->id;
            else{
              $city = new City();
              $city->name = $_POST['UserMatch']['city'];
              $city->save();
              $match->city_id = $city->id;
            }
          }else if (isset($_POST['UserMatch']['city'])) $match->city_id = null;
          
          $c = 0;
          if (isset($_POST['CollabPref'])){
            UserCollabpref::Model()->deleteAll("match_id = :match_id", array(':match_id' => $match_id));
            $c = count($_POST['CollabPref']);
            foreach ($_POST['CollabPref'] as $collab => $collab_name){
              $user_collabpref = new UserCollabpref;
              $user_collabpref->match_id = $match_id;
              $user_collabpref->collab_id = $collab;
              if ($user_collabpref->save()) $c--;
            }
          }
          
          if (($c == 0) && ($match->save())) {
            //if (Yii::app()->user->isGuest) 
            setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            //else setFlash('profileMessage', Yii::t('msg',"Profile details saved."));
            $c = new Completeness();
            $c->setPercentage($user_id);
          }else{
            setFlash('profileMessage', Yii::t('msg',"Unable to save profile details."),'alert');
          }
          
        }

        $link = new UserLink;
				$filter['user_id'] = $user_id;
				$sqlbuilder = new SqlBuilder;
        
        $this->stages = array(
          array('title'=>Yii::t('app','Profile'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>1))),
          array('title'=>Yii::t('app','Skills & preferences'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>2))),
          array('title'=>Yii::t('app','About me'),'url'=>Yii::app()->createUrl('/profile/registrationFlow',array("key"=>$_GET['key'],"email"=>$_GET['email'],"step"=>3))),
        );
        
        $c = new Completeness();
        $perc = $c->getPercentage($user_id);

        $this->layout="//layouts/stageflow";
        if($user->status == 0){
        	$data['user'] = $sqlbuilder->load_array("regflow", $filter);
        } else {
        	$data['user'] = $sqlbuilder->load_array("user", $filter);
        }
        
        if (isset($_GET['step'])){
          // if event registration redirect back to event
          if (($_GET['step'] == 4) && (isset(Yii::app()->session['event']))){
            $this->redirect(Yii::app()->createUrl('event/signup',array("id"=>Yii::app()->session['event'],"step"=>2)));
            Yii::app()->end();
          }
            
          $this->render('registrationFlow_'.$_GET['step'], array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link,'perc'=>$perc));
        }else $this->render('registrationFlow_1', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link,'perc'=>$perc));

        //if (Yii::app()->user->isGuest) $this->render('registrationFlow', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link));
        //else $this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea']));
			}    

  }
  
  
  public function actionCreateInvitation(){
    $this->layout="//layouts/none";
    
    if (!empty($_POST['invite-email'])){
    
      $user = User::model()->findByPk(Yii::app()->user->id);
 // send invitations
      if ($user){

        $invitee = User::model()->findByAttributes(array("email"=>$_POST['invite-email']));
        if ($invitee){
          setFlash("invitationMessage",Yii::t('msg','Person you invited is already in the system.'),'info');
        }else{
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

            $activation_url = Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key;

            setFlash("invitationMessage",Yii::t('msg','Invitation generated: <br /><br />').$activation_url);
          }else{
            $invitation = Invite::model()->findByAttributes(array("email"=>$_POST['invite-email']));
            $activation_url = Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key;
            setFlash("invitationMessage",Yii::t('msg','Invitation already exist: <br /><br />').$activation_url,'alert');
          }
        }// end not in system
      }
    }
    
    if (isset($_GET['invite-email'])){
      $invitation = Invite::model()->findByAttributes(array('email'=>$_GET['invite-email'],'key'=>null,'registered'=>0)); // self invited from system
      if ($invitation){
        $invitation->sender_id = Yii::app()->user->id;
        $invitation->key = md5(microtime().$invitation->email);

        if ($invitation->save()){
          $user = User::model()->findByPk(Yii::app()->user->id);
          
          // incognito tracking (no user in system yet)
          $mailTracking = mailTrackingCode();
          $ml = new MailLog();
          $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
          $ml->type = 'cofinder-invite';
          $ml->user_to_id = null;
          $ml->save();

          //$activation_url = '<a href="'.Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key.'"><strong>Register here</strong></a>';
          $activation_url = mailButton("Register here", Yii::app()->createAbsoluteUrl('/user/registration')."?id=".$invitation->key, "success", $mailTracking,'register-button');
          
          $message = new YiiMailMessage;
          $message->view = 'system';      

          $message->subject = "You have been invited to join cofinder";
          $message->setBody(array("content"=>"We've been hard at work on our new service called cofinder.
                                          Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself. 
                                          <br /><br /> <strong>".$user->name." ".$user->surname."</strong> thinks you might be the right person to test our private beta.
                                          <br /><br /> If we got your attention you can ".$activation_url."!"), 'text/html');

          $message->setTo($invitation->email);
          $message->from = Yii::app()->params['noreplyEmail'];
          Yii::app()->mail->send($message);          
          
          
          setFlash("invitationMessage",Yii::t('msg','Invitation to add new member sent.'));

          $this->refresh();
        }else setFlash("invitationMessage",Yii::t('msg','Unable to send invitation! Eather user is already invited or the email you provided is incorrect.'),'alert');
      }
    }
    
    $requests = Invite::model()->findAllByAttributes(array('key'=>null,'registered'=>0,'receiver_id'=>null,'sender_id'=>null),array('order'=>'code, id DESC'));
    
    $this->render('/profile/createInvitation',array("requests"=>$requests));
  }

  
  public function actionNotification(){
    Notifications::viewNotification(Notifications::NOTIFY_PROJECT_INVITE); //view notifications
		$user_id = Yii::app()->user->id;
		$user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
		
    $filter['user_id'] = $user_id;
    $sqlbuilder = new SqlBuilder;
    $ideas = $sqlbuilder->load_array("user", $filter);
    $ideas = $ideas['idea'];

    $invite_record = Invite::model()->findAllByAttributes(array(),"(receiver_id = :idReceiver OR email LIKE :email) AND NOT ISNULL(idea_id)",array(":idReceiver"=>$user_id,":email"=>$user->email));
    
    $invites = array();
    foreach ($invite_record as $invite){
      $idea = IdeaTranslation::model()->findByAttributes(array("idea_id"=>$invite->idea_id),array('order' => 'FIELD(language_id, 40) DESC'));

      if ($idea)
      $invites[] = array('id' => $invite->idea_id,
                         'title' => $idea->title,
                         'user' => $invite->senderId);
    }
    
    //$this->render('profile', array('user' => $user, 'match' => $match, 'data' => $data, 'link' => $link, 'ideas'=>$data['user']['idea']));
    $this->render('notifications',array('user' => $user, 'ideas'=>$ideas, "invites"=>$invites));
  }
  
   public function actionAcceptInvitation($id){
 		 $user_id = Yii::app()->user->id;
  	 $user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
     $invite_record = Invite::model()->findByAttributes(array(),"(receiver_id = :idReceiver OR email LIKE :email) AND idea_id = :idIdea",
                                                        array(":idIdea"=>$id, ":idReceiver"=>$user_id,":email"=>$user->email));
     
     if ($invite_record){
    	 $userMatch = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
       
       $ideaMember = new IdeaMember();
       $ideaMember->idea_id = $id;
       $ideaMember->match_id = $userMatch->id;
       $ideaMember->type_id = 2;
       
       if ($ideaMember->save()){
         $idea = Idea::model()->findByPk($id);
         $idea->time_updated = date('Y-m-d H:i:s');
         $idea->save();
         $invite_record->delete();
         setFlash("notificationMessage", Yii::t('msg','You have successfully joined a project.'));
       }
     }
     
     $this->redirect(Yii::app()->createUrl("profile/notification"));
   }
  
   public function actionDeclineInvitation($id){
 		 $user_id = Yii::app()->user->id;
  	 $user = UserEdit::Model()->findByAttributes(array('id' => $user_id));
     $invite_record = Invite::model()->findByAttributes(array(),"(receiver_id = :idReceiver OR email LIKE :email) AND idea_id = :idIdea",
                                                        array(":idIdea"=>$id, ":idReceiver"=>$user_id,":email"=>$user->email));
     
     if ($invite_record) $invite_record->delete();
     
     setFlash("notificationMessage", Yii::t('msg','Invitation removed!'));
     $this->redirect(Yii::app()->createUrl("profile/notification"));
   }
   
  public function actionCompleteness() {
    $comp = new Completeness();
    $ungrouped = $comp->init();
    
    //echo $comp->setPercentage($user_id);
    
    $data = array();
    foreach ($ungrouped as $row){
      $data[$row['group']][] = $row;
    }
    
    $this->render('completeness',array('data' => $data));
  }
}
