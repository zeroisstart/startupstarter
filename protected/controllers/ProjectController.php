<?php

class ProjectController extends GxController {

//	public $data = array();
	public $layout="//layouts/view";
	
	/**
	 * @return array action filters
	 */
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
//        'actions'=>array("view","discover","embed"),
        'actions'=>array("view","embed","discover"),
				'users'=>array('*'),
			),
	    array('allow',
		        'actions'=>array('create','edit','leaveIdea','deleteIdea','addMember','deleteMember','sAddSkill','sDeleteSkill',
                             'sAddLink','sDeleteLink', 'addLink','deleteLink', 'translate','deleteTranslation','suggestMember'),
		        'users'=>array("@"),
		    ),
			array('allow', 
        'actions'=>array("recent"),  // remove after demo
				'users'=>array('@'),
			),
			array('allow', // allow admins only
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($lang = NULL) {
		$filter = Yii::app()->request->getQuery('filter', array());
		
		$sqlbuilder = new SqlBuilder;
		if($lang){
			$filter['lang'] = $lang;
		}

		$data['idea'] = $sqlbuilder->load_array("idea", $filter);

		$this->render('index', array('data' => $data));
	}

	public function actionView($id, $lang = NULL) {
		$this->layout = "//layouts/none";
    
		$sqlbuilder = new SqlBuilder;
		$filter['idea_id'] =  $id;
		if($lang){
			$filter['lang'] = $lang;
		}

		$data['idea'] = $sqlbuilder->load_array("idea", $filter, "translation_other,link,member,gallery,candidate,skill,industry,collabpref");

		if(!isset($data['idea']['id'])){
			throw new CHttpException(400, Yii::t('msg', "Oops! This project does not exist."));
		}

		//log clicks
		$click = new Click;
		$click->idea($id, Yii::app()->user->id);

    $lastMsg = '';
    if (!Yii::app()->user->isGuest)
      $lastMsg = Message::model()->findByAttributes(array('user_from_id'=>Yii::app()->user->id,'idea_to_id'=>$id),array('order'=>'time_sent DESC'));
    
		$this->render('view', array('data' => $data,'lastMsg'=>$lastMsg));
	}
  
  /**
   * suggest members
   */
  public function actionSuggestMember($term){
    
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

	public function actionEdit(){
		//1. korak - splošni podatki
		//ID prebrat iz sešna, če je že, naloadat podatke, drugače nič
		//naloadat modele
		//naloadat view
		switch($step){
			case 1:
				//1. korak - splošni podatki
				$this->actionCreateStep1();
				break;
			case 2:
				//2. korak - koga iščemo

				break;	
			case 3:
				//3. korak - story

				break;
			case 4:
				//4. korak - dodajanje linkov in ostalo

				break;

		}

	}


	public function actionCreateStep1(){

		if(isset($_SESSION['idea_id'])){
			$idea = Idea::Model()->findByAttributes(array('id' => $_SESSION['idea_id']));
			$translation = IdeaTranslation::Model()->findByAttributes(array('idea_id' => $_SESSION['idea_id']));

			if(isset($_POST[' ']))
		} else {
			$idea = new Idea;
			$translation = new IdeaTranslation;
		}

		if(isset($_POST['']))

		$language = Language::Model()->findByAttributes( array( 'language_code' => Yii::app()->language ) );

		$this->render('createidea_1', array( 'idea' => $idea, 'translation' => $translation, 'language' => $language ));

	}

	public function actionEdit($id){ //can take different languages to edit

	}

	public function addKeywords($idea_id, $language_id, $keywords){
		Keyword::Model()->deleteAll("keyword.table = :table AND row_id = :row_id", 
                                 array(':table' => 'idea_translation', ':row_id' => $idea_id));

		$keyworder = new Keyworder;
		$keywords = $keyworder->string2array($keywords);

		foreach($keywords AS $key => $word){
          $keyword = new Keyword;
          $keyword->table = 'idea_translation';
          $keyword->row_id = $idea_id;
          $keyword->keyword = $word;
          $keyword->language_id = $language_id;
          $keyword->save();
		}
	}

	public function actionTranslate($id) {

		//general layout
		$this->layout="//layouts/edit";

		//for sidebar purposes
		$sqlbuilder = new SqlBuilder;
		$user_id = Yii::app()->user->id;
		$filter['user_id'] = $user_id;
		unset($filter['lang']);
		//$data['user'] = $sqlbuilder->load_array("user", $filter);

		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$criteria=new CDbCriteria();
		$criteria->addInCondition('type_id',array(1,2)); //members
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $id), $criteria);
    
		if($idea && $hasPriviledges){

			$translation = new IdeaTranslation;

      if(!isset($_POST['Idea']) AND !isset($_POST['IdeaTranslation'])){
        //$translation->language_id = 40;
        $translation->description_public = 1;
      }
      
			if (isset($_POST['IdeaTranslation'])) {
				$_POST['IdeaTranslation']['idea_id'] = $id;

				$exists = IdeaTranslation::Model()->findByAttributes( array( 'idea_id' => $idea->id, 'language_id' => $_POST['IdeaTranslation']['language_id'], 'deleted' => 0 ) );
				if($exists){
					$language = $this->loadModel($exists->language_id, 'Language');
					$this->redirect(Yii::app()->createUrl("project/edit", array('id' => $id, "lang"=>$language->language_code)));
				}

				$translation->setAttributes($_POST['IdeaTranslation']);
				if ($translation->save()) {
					$time_updated = new TimeUpdated;
					$time_updated->idea($id);

					//break up keywords and save
		 			$this->addKeywords($id, $translation->language_id, $_POST['IdeaTranslation']['keywords']);

		 			$language = Language::Model()->findByAttributes(array('id' => $translation->language_id));

		 			setFlash('projectMessage', Yii::t('msg',"The translation of the project was successfully saved."));

					$this->redirect(array('edit', 'id' => $id, 'lang' => $language->language_code));
				} else {
					setFlash('projectMessage', Yii::t('msg',"Could not save project translation."),'alert');
				}
			}

			$this->render('createtranslation', array( 'idea' => $idea, 'translation' => $translation ));
		}else throw new CHttpException(400, Yii::t('msg', 'Your request is invalid.'));
	}

	public function actionDeleteTranslation($id, $lang) {
		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$criteria=new CDbCriteria();
		$criteria->addInCondition('type_id',array(1,2)); //members
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $id), $criteria);

		if($idea && $hasPriviledges){

			$sql = "SELECT count(id) FROM idea_translation WHERE idea_id = $id AND deleted = 0";
			$numTranslations = Yii::app()->db->createCommand($sql)->queryScalar();
			if($numTranslations > 1){
				$language = Language::Model()->findByAttributes( array( 'language_code' => $lang ) );
				$translation = IdeaTranslation::Model()->findByAttributes( array( 'idea_id' => $idea->id, 'language_id' => $language->id, 'deleted' => 0 ) );

				$translation->setAttributes(array('deleted' => 1));

				if ($translation->save()) {
					$return['message'] = Yii::t('msg', "Translation successfully removed!");
					$return['status'] = 0;

					$time_updated = new TimeUpdated;
					$time_updated->idea($id);
          setFlash('projectMessage', Yii::t('msg',"Translation successfully removed!"));
				} else {
					$return['message'] = Yii::t('msg', "Unable to remove translation from the project.");
					$return['status'] = 1;
          setFlash('projectMessage', Yii::t('msg',"Unable to remove translation from the project."),'alert');
				}
				
				if(isset($_GET['ajax'])){
					$return = htmlspecialchars(json_encode($return), ENT_NOQUOTES);
					echo $return; //return array
					Yii::app()->end();
				}
        

        $this->redirect(Yii::app()->createUrl('project/edit',array('id'=>$id)));
        
			}
		}
	}

	public function actionDeleteIdea($id) {
		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );
		
		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));

    	$ideaMember = IdeaMember::Model()->findByAttributes(array('type_id' => 1,'match_id' => $match->id, 'idea_id' => $id));
		if($idea && $ideaMember){
			$idea->deleted = 1;
				
			if($idea->save()){
        setFlash('removeProjectsMessage', Yii::t('msg',"Project successfully removed."));
			}
		}

    	$this->redirect(Yii::app()->createUrl('profile/projects'));
	}
  
	public function actionLeaveIdea($id){
		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));

	    $ideaMember = IdeaMember::Model()->findByAttributes(array('type_id' => 2,'match_id' => $match->id, 'idea_id' => $id));
	    if($ideaMember && $ideaMember->delete()){
	      	setFlash('projectMessage', Yii::t('msg',"Project removed from your account successfully."));
	    } else {
	    	setFlash('projectMessage', Yii::t('msg',"Could not remove project from your account."),'alert');
	    }
	    $this->redirect(Yii::app()->createUrl('profile/projects'));
	}

	//ajax functions
	public function actionSAddSkill() {

		//check for permission
		$user_id = Yii::app()->user->id;

		//status
		$status = 1;

		if(isset($_SESSION['Candidate']['id']) && $user_id > 0){

			if (!empty($_POST['skill']) && !empty($_POST['skillset'])) {

				$skillset = Skillset::model()->findByPk($_POST['skillset']);
				if($skillset){
					$key = $_POST['skillset'] . "_" . $_POST['skill'];

					if(!isset($_SESSION['Candidate']['skills'][$key])){
						$_SESSION['Candidate']['skills'][$key]['skillset_id'] = $_POST['skillset']; //id

						$language = Language::Model()->findByAttributes( array( 'language_code' => Yii::app()->language ) );
						if($language->id == 40){
							$_SESSION['Candidate']['skills'][$key]['skillset_name'] = $skillset->name; //id$skillset->name
						} else {
							$translation = Translation::Model()->findByAttributes(array('language_id' => $language->id, 'table' => 'skillset', 'row_id' => $skillset->id));
							$_SESSION['Candidate']['skills'][$key]['skillset_name'] = $translation->translation; //id$skillset->name
						}

						$_SESSION['Candidate']['skills'][$key]['skill'] = $_POST['skill']; //skill name

						$status = 0;
					} else $status = 1;

				} else $status = 2;
			}

		} else $status = 3;

		if($status == 0){
			$response = array("data" => array("title" => $_POST['skill'],
			                                "id" => $key,
			                                "location" => Yii::app()->createUrl("project/sDeleteSkill"),
			                                "desc" => $_SESSION['Candidate']['skills'][$key]['skillset_name'], 
                                      "multi" => 1
			                ),
			"status" => 0,
			"message" => "");
		} else {
			$response = array("data" => null,
							"status" => 1,
							"message" => Yii::t('msg', "Problem saving skill. Please check fields for correct values."));
		}
    	echo json_encode($response);
    	Yii::app()->end();
       
	}

	public function actionSDeleteSkill() {
    
		if (isset($_SESSION['Candidate']['skills'][$_POST['id']])){
			unset($_SESSION['Candidate']['skills'][$_POST['id']]);
			$return['message'] = '';
			$return['status'] = 0;
		} else {
			$return['message'] = Yii::t('msg', "Unable to remove skill.");
			$return['status'] = 1;
		}

		if (isset($_GET['ajax'])) {
			$return = json_encode($return);
			echo $return; //return array
			Yii::app()->end();
		} else {
			//not ajax stuff
		}
	}

	public function actionAddMember($id) {

		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$criteria=new CDbCriteria();
		$criteria->addInCondition('type_id',array(1)); //owner
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $id), $criteria);

		if($idea && $hasPriviledges){

			$member = new IdeaMember;

			if (isset($_POST['IdeaMember'])) {

				$_POST['IdeaMember']['idea_id'] = $id;

				$match = UserMatch::Model()->findByAttributes( array( 'user_id' => $_POST['IdeaMember']['user_id'] ) );
				$_POST['IdeaMember']['idea_id'] = $id;
				$_POST['IdeaMember']['match_id'] = $match->id;
				$_POST['IdeaMember']['type_id'] = '2'; //HARDCODED MEMBER

				$exists = IdeaMember::Model()->findByAttributes( array( 'match_id' => $match->id, 'idea_id' => $id ) );
				if(!$exists){

					$member->setAttributes($_POST['IdeaMember']);

					if ($member->save()) {
						$return['status'] = 0;

						$time_updated = new TimeUpdated;
						$time_updated->idea($id);
					} else {
						$return['message'] = Yii::t('msg', "Oops! Something went wrong. Unable to add a new member to the project.");
						$return['status'] = 1;
					}
					
					if(isset($_GET['ajax'])){
						$return = htmlspecialchars(json_encode($return), ENT_NOQUOTES);
						echo $return; //return array
						Yii::app()->end();
					}					
				}
			}
		}
	}

	public function actionDeleteMember($id, $user_id) {
		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $id,'type_id'=>1));

		if($idea && $hasPriviledges){
			$match = UserMatch::Model()->findByAttributes(array('user_id' => $user_id));
			$member = IdeaMember::Model()->findByAttributes( array( 'match_id' => $match->id, 'idea_id' => $id ) );

			if($member->delete()){
				$return['status'] = 0;

        $idea->time_updated = date('Y-m-d H:i:s');
        $idea->save();
				//$time_updated = new TimeUpdated;
				//$time_updated->idea($id);
        //setFlash("projectMessage", Yii::t('msg','Member removed from the project'));
        setFlash('projectMessage', Yii::t('msg','Member removed from the project'));
			} else {
				$return['message'] = Yii::t('msg', "Oops! Something went wrong. Unable to remove member from the project.");
				$return['status'] = 1;
			}
			
			if(isset($_GET['ajax'])){
				$return = htmlspecialchars(json_encode($return), ENT_NOQUOTES);
				echo $return; //return array
				Yii::app()->end();
			} else {
        $this->redirect(Yii::app()->request->urlReferrer);
	           	//not ajax stuff
			}
		}else throw new CHttpException(400, Yii::t('msg', 'Your request is invalid.'));
	}

	//used before the idea is created
	public function actionSAddLink() {
		//check for permission
		$user_id = Yii::app()->user->id;

		//idea does not exist yet, no use in checking anything

		//status
		$status = 1;

		if (!empty($_POST['IdeaLink']['title']) && !empty($_POST['IdeaLink']['url'])) {

			$key = $_POST['IdeaLink']['url'];

			if(!isset($_SESSION['Links'][$key])){

				$_SESSION['Links'][$key] = $_POST['IdeaLink']['title']; //url -> title
				$status = 0;
			} else $status = 1;
		}

		if($status == 0){
			$response = array("data" => array("title" => $_POST['IdeaLink']['title'],
			                                "id" => $key,
			                                "location" => Yii::app()->createUrl("project/sDeleteLink"),
			                                "url" => $key,
			                                "session" => addslashes(serialize($_SESSION)),
                                      "multi" => 1
			                ),
			"status" => 0,
			"message" => "");
		} else {
			$response = array("data" => null,
							"status" => 1,
							"message" => Yii::t('msg', "Problem saving link. Please check fields for correct values."));
		}
    	echo json_encode($response);
    	Yii::app()->end();
	}
	//used before the idea is created
	public function actionSDeleteLink() {
		if (isset($_SESSION['Links'][$_POST['id']])){
			unset($_SESSION['Links'][$_POST['id']]);
			$return['message'] = $_POST['id'];
			$return['status'] = 0;
		} else {
			$return['message'] = Yii::t('msg', "Unable to remove link.");
			$return['status'] = 1;
		}

			$return = json_encode($return);
			echo $return; //return array
			Yii::app()->end();
	}

	public function actionAddLink($id, $post = false) {

		if($post != false){
			$_POST = $post;
		}

		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $idea->id,'type_id'=>1));

		if($idea && $hasPriviledges){

			$link = new IdeaLink;

			if (isset($_POST['IdeaLink'])) {

				$_POST['IdeaLink']['idea_id'] = $idea->id;
				$_POST['IdeaLink']['url'] = str_replace("http://", "", $_POST['IdeaLink']['url']);

				$exists = IdeaLink::Model()->findByAttributes(array('idea_id' => $idea->id, 'url' => $_POST['IdeaLink']['url']));
				if (!$exists) {

					$link->setAttributes($_POST['IdeaLink']);

					if ($link->save()) {
						$response = array("data" => array("title" => $_POST['IdeaLink']['title'],
										"url" => $_POST['IdeaLink']['url'],
										"id" => $_POST['IdeaLink']['url'],
										"location" => Yii::app()->createUrl("project/deleteLink/".$idea->id)
								),
								"status" => 0, // a damo console status kjer je 0 OK vse ostale cifre pa error????
								"message" => Yii::t('msg', "Link successfully saved to project."));
					} else {
						$response = array("data" => null,
								"status" => 1,
								"message" => Yii::t('msg', "Problem saving link. Please check fields for correct values."));
					}
				} else {
					$response = array("data" => null,
							"status" => 1,
							"message" => Yii::t('msg', "Project already has this link."));
				}

				if($post == false){
					echo json_encode($response);
					Yii::app()->end();
				} else {
					return $response;
				}
			}
		}
	}

	public function actionDeleteLink($id) {

		$idea = Idea::Model()->findByAttributes( array( 'id' => $id, 'deleted' => 0 ) );

		$match = UserMatch::Model()->findByAttributes(array('user_id' => Yii::app()->user->id));
		$hasPriviledges = IdeaMember::Model()->findByAttributes(array('match_id' => $match->id, 'idea_id' => $idea->id,'type_id'=>1));

		if($idea && $hasPriviledges){

			$url = 0;
			if (isset($_POST['id']))
				$url = $_POST['id'];

			if ($idea->id > 0 && $url) {

				$link = IdeaLink::Model()->findByAttributes(array('url' => $url,'idea_id' => $idea->id));

				if ($link->delete()) {
					$response = array("data" => array("id" => $link_id),
							"status" => 0,
							"message" => "Link successfully removed from project.");
				} else {
					$response = array("data" => null,
							"status" => 1,
							"message" =>  Yii::t('msg', "Unable to remove link."));
				}
			}
		}

		echo json_encode($response);
		Yii::app()->end();
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

		echo $return; // it's array
	}

	public function uploadToGallery($id, $link, $cover = true){

		/*
		add to gallery
			-action to upload image
		edit gallery
			-action/view to edit gallery
		set cover image
			-action to 
		remove from gallery
			-action to unlink image/unset from db
		*/

		$filename = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['tempFolder'] . $link;

		// if we need to create avatar image
		if (is_file($filename)) {
			$newFilePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . Yii::app()->params['ideaGalleryFolder'];
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
				$thumb->create($newFilePath . $newFileName)
								->resize(150, 150)
								->save($newFilePath . "thumb_150_" . $newFileName);

				//insert
				$idea_gallery = new IdeaGallery;
				$idea_gallery->cover = 0;
				if($cover == true){
					$idea_gallery->cover = 1;
					IdeaGallery::model()->updateAll(array('cover'=>0),"cover='1' AND idea_id='{$id}'"); 
				}
				
				$idea_gallery->url = $newFileName;
				$idea_gallery->idea_id = $id;

				$idea_gallery->save();

				return $newFileName;
			}
		}

		return false;
	}

	public function sessionReset($type){

		if($type == 'candidate'){
			if(isset($_SESSION['Candidate']['collabprefs'])){
				foreach($_SESSION['Candidate']['collabprefs'] as $key => $value){
					unset($_SESSION['Candidate']['collabprefs'][$key]);
				}
				unset($_SESSION['Candidate']['collabprefs']);
			}
			if(isset($_SESSION['Candidate']['skills'])){
				foreach($_SESSION['Candidate']['skills'] as $key => $value){
					unset($_SESSION['Candidate']['skills'][$key]);
				}
				unset($_SESSION['Candidate']['skills']);
			}
			if(isset($_SESSION['Candidate']['id'])){
				unset($_SESSION['Candidate']['id']);
			}
			if(isset($_SESSION['Candidate'])){
				unset($_SESSION['Candidate']);
			}		
			
		} elseif($type == 'idea'){
			unset($_SESSION['IdeaCreated']);
		} elseif($type == 'links'){
			if(isset($_SESSION['Links'])){
				foreach($_SESSION['Links'] as $key => $value){
					unset($_SESSION['Links'][$key]);
				}
				unset($_SESSION['Links']);
			}
		}
	}
  
  public function actionEmbed($id){
    $this->layout="//layouts/blank";
    
		$sqlbuilder = new SqlBuilder;
		$filter = array( 'idea_id' => $id);

		$this->render('embed', array('idea' => $sqlbuilder->load_array("idea", $filter)));
  }  
  

  public function actionDiscover($id = 1){
    $this->layout="//layouts/none";
    
		$sqlbuilder = new SqlBuilder;
		$filter = Yii::app()->request->getQuery('filter', array());
    
    if (Yii::app()->user->isGuest){
      $_GET['SearchForm'] = '';
      $filter['per_page'] = 3;
      $filter['page'] = 1;
      $register = '<a href="'.Yii::app()->createUrl("user/registration").'" class="button small radius secondary ml10 mb0">'.Yii::t('app','register').'</a>';
      setFlash("discoverPerson", Yii::t('msg','Only recent three results are shown!<br />To get full functionality please login or {register}',array('{register}'=>$register)), "alert", false);
    }else{
    	if(isset($_GET['ajax'])){
    		$filter['per_page'] = 3;
    	} else {
    		$filter['per_page'] = 6;
    	}
      	$filter['page'] = $id;
    }
    
    $searchForm = new SearchForm();
    $searchForm->isProject = true;
    
    $searchResult = array();
		
	if (isset($_GET['SearchForm'])) $searchForm->setAttributes($_GET['SearchForm']);
		
    if ($searchForm->checkSearchForm()){
			// search results
      	$searchForm->setAttributes($_GET['SearchForm']);
			
			$filter['available'] = $searchForm->available;
			$filter['city'] = $searchForm->city;
			$filter['collabpref'] = $searchForm->collabPref;
			$filter['skill'] = $searchForm->skill;
			$filter['stage'] = $searchForm->stage;
			
			$search = $sqlbuilder->load_array("search_ideas", $filter, "translation,member,candidate,skill,industry");
			$searchResult['data'] = $search['results'];
			$count = $search['count'];

			$searchResult['page'] = $id;
			$searchResult['maxPage'] = ceil($count / $filter['per_page']); //!!! add page count

			$ideaType = Yii::t('app', "Found projects");
    }else{
    	if(!Yii::app()->user->isGuest && isset($_SESSION['suggested']) && $_SESSION['suggested'] == true){
    		$filter = new FilterFromProfile;
    		$filter = $filter->search("ideaByProfile", Yii::app()->user->id);
    		$filter['page'] = $id;

		    if(isset($_GET['ajax'])) $filter['per_page'] = 3;
		    else $filter['per_page'] = 6;

    		$filter['recent'] = 'recent';
    		$filter['where'] = "AND i.time_updated > ".(time() - 3600 * 24 * 14);
    		$search = $sqlbuilder->load_array("search_ideas", $filter, "translation,member,candidate,skill,industry");
    		$ideaType = Yii::t('app', "Suggested projects");
    		
			//if there's not plenty of results...
			if($search['count'] < 3){
			 	$filter['where'] = "AND i.time_updated > ".(time() - 3600 * 24 * 31);
				$search = $sqlbuilder->load_array("search_users", $filter, "translation,member,candidate,skill,industry");
				if($search['count'] < 3){
		  			$search['results'] = $sqlbuilder->load_array("recent_ideas", $filter, "translation,member,candidate,skill,industry");
					$search['count'] = $count = $sqlbuilder->load_array("count_ideas", $filter);
					$ideaType = Yii::t('app', "Recent projects");
				}
			}

			$searchResult['data'] = $search['results'];
			$searchResult['page'] = $id;
			$searchResult['maxPage'] = ceil($search['count'] / $filter['per_page']);
    	} else {
      		$count = $sqlbuilder->load_array("count_ideas", $filter);

			$searchResult['data'] = $sqlbuilder->load_array("recent_ideas", $filter, "translation,member,candidate,skill,industry");
			$searchResult['page'] = $id;
			$searchResult['maxPage'] = ceil($count / $filter['per_page']); ; //!!! add page count
    	
			$ideaType = Yii::t('app', "Recent projects");
    	}
    }
	
    if(isset($_GET['ajax'])){
		$return['data'] = $this->renderPartial('_recent', array("ideas" => $searchResult['data'], 'page' => $id, 'maxPage' => $searchResult['maxPage'], 'ideaType' => $ideaType), true);
		$return['message'] = '';//Yii::t('msg', "Success!");
		$return['status'] = 0;
		$return = json_encode($return);
		echo $return; //return array
		Yii::app()->end();
    } else {
		$this->render('discover', array("filter"=>$searchForm, "searchResult"=>$searchResult, "ideaType"=>$ideaType));
  	}
  }

	public function actionSuggestUser() {

		if (!isset($_GET['term'])){
			$response = array("data" => null,
								"status" => 1,
								"message" => Yii::t('msg', "No search query."));
		}else{
			$connection=Yii::app()->db;
			$data = array();

			$value = $_GET['term'];

				//find by name
				$criteria=new CDbCriteria();
				$criteria->condition = " `name` LIKE :value OR `surname` LIKE :value"; // OR `email` LIKE :value";
				$criteria->params = array(":value"=>"%".$value."%");
				$criteria->order = "name";
				
				$dataReader = UserEdit::model()->findAll($criteria);
				foreach ($dataReader as $row){
					$data[] = array("name"=>$row['name']." ".$row['surname'], 
                          "email"=>$row['email'], 
                          "id"=>$row['id'], 
                          "avatar"=>avatar_image($row['avatar_link'], $row['id'], 30));
                      
				}

			
			$response = array("data" => $data,
												"status" => 0,
												"message" => '');
		}
		
		echo json_encode($response);
		Yii::app()->end();
	}

}
