<?php

/**
 * This is the model base class for the table "user".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "UserEdit".
 *
 * Columns in table "user" available as properties of the model,
 * followed by relations of table "user" available as properties of the model.
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $activkey
 * @property string $create_at
 * @property string $lastvisit_at
 * @property integer $superuser
 * @property integer $status
 * @property string $name
 * @property string $surname
 * @property string $address
 * @property string $avatar_link
 * @property integer $language_id
 * @property integer $newsletter
 * @property string $vanityURL
 * @property string $bio
 *
 * @property ClickIdea[] $clickIdeas
 * @property ClickUser[] $clickUsers
 * @property ClickUser[] $clickUsers1
 * @property UserLink[] $userLinks
 * @property UserMatch[] $userMatches
 */
abstract class BaseUserEdit extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'user';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'User|Users', $n);
	}

	public static function representingColumn() {
		return 'email';
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('superuser, status, language_id, newsletter', 'numerical', 'integerOnly'=>true),
			array('email, password, activkey, name, surname, address, avatar_link', 'length', 'max'=>128),
			array('personal_achievement', 'length', 'max'=>140),
			array('lastvisit_at, bio', 'safe'),
			array('vanityURL', 'unique', 'message' => Yii::t('msg',"This public name is already taken.")),
			array('activkey, lastvisit_at, superuser, status, surname, address, avatar_link, language_id, newsletter, vanityURL, bio', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, email, password, activkey, create_at, lastvisit_at, superuser, status, name, surname, address, bio, avatar_link, language_id, newsletter', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'clickIdeas' => array(self::HAS_MANY, 'ClickIdea', 'user_id'),
			'clickUsers' => array(self::HAS_MANY, 'ClickUser', 'user_id'),
			'clickUsers1' => array(self::HAS_MANY, 'ClickUser', 'user_click_id'),
			'userLinks' => array(self::HAS_MANY, 'UserLink', 'user_id'),
			'userMatches' => array(self::HAS_ONE, 'UserMatch', 'user_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'email' => Yii::t('app', 'Email'),
			'password' => Yii::t('app', 'Password'),
			'activkey' => Yii::t('app', 'Activation key'),
			'create_at' => Yii::t('app', 'Registered'),
			'lastvisit_at' => Yii::t('app', 'Last visited'),
			'superuser' => Yii::t('app', 'Superuser'),
			'status' => Yii::t('app', 'Status'),
			'name' => Yii::t('app', 'First name'),
			'surname' => Yii::t('app', 'Last name'),
			'address' => Yii::t('app', 'Address'),
			'bio' => Yii::t('app', 'Personal pitch'),
			'avatar_link' => Yii::t('app', 'Avatar link'),
			'language_id' => Yii::t('app', 'Page language'),
			'newsletter' => Yii::t('app', 'Newsletter'),
      'vanityURL' => Yii::t('app', 'Public name'),
			'clickIdeas' => null,
			'clickUsers' => null,
			'clickUsers1' => null,
			'userLinks' => null,
			'userMatches' => null,
      'personal_achievement' => Yii::t('app', 'Biggest accomplishment'), 
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('activkey', $this->activkey, true);
		$criteria->compare('create_at', $this->create_at, true);
		$criteria->compare('lastvisit_at', $this->lastvisit_at, true);
		$criteria->compare('superuser', $this->superuser);
		$criteria->compare('status', $this->status);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('surname', $this->surname, true);
		$criteria->compare('address', $this->address, true);
		$criteria->compare('bio', $this->bio);
		$criteria->compare('avatar_link', $this->avatar_link, true);
		$criteria->compare('language_id', $this->language_id);
		$criteria->compare('newsletter', $this->newsletter);
		$criteria->compare('vanityURL', $this->vanityURL);
		$criteria->compare('personal_achievement', $this->personal_achievement);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}