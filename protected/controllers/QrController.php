<?php

class QrController extends Controller
{

	public $layout="//layouts/card";

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
				'users'=>array('*'),
			),
			array('allow', // allow admins only
				'users'=>Yii::app()->getModule('user')->getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
  
  /**
   * create code
   */
	public function actionCreate() {
    $qrLogin = new QrLogin();
    $qrLogin->save();
    
    $hashids = new Hashids('cofinder');
    
    //echo Yii::app()->createAbsoluteUrl("/qr/scan",array("qr"=>$qrLogin->id));
    echo $hashids->encrypt_hex($qrLogin->id);
    Yii::app()->end();
	}
  
  
  /**
   * check validity
   */
	public function actionValidate($qr) {
    $hashids = new Hashids('cofinder');
    $id = $hashids->decrypt_hex($qr);

    $qrLogin = QrLogin::model()->findByPk($id);
    if (!$qrLogin){
      echo false;
      return;
    }
    
    // code has expired
    $td = timeDifference(time(), $qrLogin->create_at,'minutes_total');
    if ($td >= 5){
      echo false;
      return;
    }
    
    // check code
    if ($qrLogin && $qrLogin->user_id){
      $identity=new UserIdentity($qrLogin->user->email,$qrLogin->user->password);
      $identity->qrcode = true;
      if ($identity->authenticate()) Yii::app()->user->login($identity);
      
      $this->log('qr','validate',$qrLogin->user_id);
      
      $qrLogin->delete(); // one time use
      echo true;
    }else echo false;
    Yii::app()->end();
	}

  /**
   * scann the code
   */
	public function actionScan($qr) {
    
    $hashids = new Hashids('cofinder');
    $id = $hashids->decrypt_hex($qr);
    
    $qrLogin = QrLogin::model()->findByPk($id);
    
    if (!$qrLogin || $qrLogin->user_id){
      $this->render('//site/message',array("title"=>Yii::t('msg','Problem scaning code'),"content"=>Yii::t('msg','Something went wrong while scaning the code!<br /> Please refresh the page and rescan the code.')));
      return;
    }

    $this->log('qr','scan');

    // check validity of token (5min)
    $td = timeDifference(time(), $qrLogin->create_at,'minutes_total');
    if ($td < 5){
      if ($qrLogin->user_id == null && $qrLogin->scan_at == null){
        $qrLogin->scan_at = date('Y-m-d H:i:s');
        $qrLogin->save();
      }
      
      // has cookie auto login
      if (Yii::app()->request->cookies->contains('mblg')){
        $code = Yii::app()->request->cookies['mblg']->value;
        $usr = User::model()->findByAttributes(array('qrcode'=>$code));
        
        if ($usr){
          $qrLogin->user_id = $usr->id;
          $qrLogin->save();
          // user found
          $this->render('//site/message',array('title'=>Yii::t('msg','Loged in'),"content"=>Yii::t('msg','You should be loged in shortly.')));
        }else{
          // no user
          unset(Yii::app()->request->cookies['mblg']);
          $this->redirect(Yii::app()->createUrl("qr/login",array("qr"=>$qr)));
          //$this->render('//site/message',array("title"=>Yii::t('msg','Problem scaning code'),"content"=>Yii::t('msg','Something went wrong while scaning the code!<br /> Please refresh the page and rescan the code.')));
        }
        return;
      }
     
      // go to login form and login from phone
      $this->redirect(Yii::app()->createUrl("qr/login",array("qr"=>$qr)));
    }
    else{
      $this->render('//site/message',array('title'=>Yii::t('msg','Code expired'),"content"=>Yii::t('msg','The code has expired!<br /> Please refresh the page and rescan the code.')));
      return;
    }
    
    
  }
  
  
  /**
   * login user if not already
   */
	public function actionLogin($qr) {
    $hashids = new Hashids('cofinder');
    $id = $hashids->decrypt_hex($qr);
    
    $qrLogin = QrLogin::model()->findByPk($id);
    if (!$qrLogin || $qrLogin->user_id){
      $this->render('//site/message',array("title"=>Yii::t('msg','Problem scaning code'),"content"=>Yii::t('msg','Something went wrong while scaning the code!<br /> Please refresh the page and rescan the code.')));
      return;
    }
    // check validity of token (5min)
    $td = timeDifference(time(), $qrLogin->create_at,'minutes_total');
    if ($td >= 5){
      $this->render('//site/message',array('title'=>Yii::t('msg','Code expired'),"content"=>Yii::t('msg','The code has expired!<br /> Please refresh the page and rescan the code.')));
      return;
    }
    
    $model=new LoginForm();
    
    if(isset($_POST['LoginForm'])){
      $model->attributes=$_POST['LoginForm'];
      $model->rememberMe = false;
      // validate user input and redirect to previous page if valid
      if($model->validate()) {
        $identity = new UserIdentity($model->username,$model->password);
        if ($identity->authenticate()){
          $user = User::model()->findByAttributes(array('email'=>$model->username));
          
          //qr code set
          $code = UserModule::encrypting(microtime().$model->username);
          $user->qrcode = $code; //set code
          $user->save();
          
          //user
          $qrLogin->user_id = $user->id;
          $qrLogin->save();
          
          Yii::app()->request->cookies['mblg'] = new CHttpCookie('mblg', $code, 
                            array(//'domain'=>'.cofinder.eu',
                                  'expire'=>time()+60*60*24*30*12,  //1 year
                                  //'secure'=>'',
                                  'httpOnly'=>true
                            ));
          $this->render('//site/message',array('title'=>Yii::t('msg','Loged in'),"content"=>Yii::t('msg','You should be loged in shortly.')));
          return;
        }
      }
      
    }
    
    $this->render('login',array("model"=>$model));
	}
  
}
