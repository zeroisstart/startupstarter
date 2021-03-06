<?php
/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping
 * user change password form data. It is used by the 'changepassword' action of 'UserController'.
 */
class UserChangePassword extends CFormModel {
	public $oldPassword;
	public $password;
	public $verifyPassword;
	
	public function rules() {
		return Yii::app()->controller->id == 'recovery' ? array(
			array('password, verifyPassword', 'required'),
			array('password, verifyPassword', 'length', 'max'=>128, 'min' => 6,'message' => Yii::t('msg',"Incorrect password (minimal length 6 symbols).")),
			array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => Yii::t('msg',"Retype password is incorrect.")),
		) : array(
			array('oldPassword, password, verifyPassword', 'required'),
			array('oldPassword', 'length', 'max'=>128, 'min' => 4,'message' => Yii::t('msg',"Incorrect password (minimal length 4 symbols).")),
			array('password, verifyPassword', 'length', 'max'=>128, 'min' => 6,'message' => Yii::t('msg',"Incorrect password (minimal length 4 symbols).")),
			array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => Yii::t('msg',"Retype password is incorrect.")),
			array('oldPassword', 'verifyOldPassword'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'oldPassword'=>Yii::t('app',"Old password"),
			'password'=>Yii::t('app',"New password"),
			'verifyPassword'=>Yii::t('app',"Retype password"),
		);
	}
	
	/**
	 * Verify Old Password
	 */
	 public function verifyOldPassword($attribute, $params)
	 {
		 if (!Yii::app()->getModule('user')->validate($this->$attribute,User::model()->notsafe()->findByPk(Yii::app()->user->id)->password))
			 $this->addError($attribute, Yii::t('msg',"Old Password is incorrect."));
	 }
}