<?php

/**
 * This is the model base class for the table "click_user".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ClickUser".
 *
 * Columns in table "click_user" available as properties of the model,
 * followed by relations of table "click_user" available as properties of the model.
 *
 * @property string $id
 * @property string $time
 * @property string $user_id
 * @property string $user_click_id
 *
 * @property User $user
 * @property User $userClick
 */
abstract class BaseClickUser extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'click_user';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'ClickUser|ClickUsers', $n);
	}

	public static function representingColumn() {
		return 'userClick';
	}

	public function rules() {
		return array(
			array('user_id, user_click_id', 'required'),
			array('user_id, user_click_id', 'length', 'max'=>11),
			array('id, time_registered, time_updated, user_id, user_click_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'userClick' => array(self::BELONGS_TO, 'User', 'user_click_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'time' => Yii::t('app', 'Time'),
			'user_id' => null,
			'user_click_id' => null,
			'user' => null,
			'userClick' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('time', $this->time, true);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('user_click_id', $this->user_click_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}