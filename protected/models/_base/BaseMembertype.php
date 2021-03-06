<?php

/**
 * This is the model base class for the table "membertype".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Membertype".
 *
 * Columns in table "membertype" available as properties of the model,
 * followed by relations of table "membertype" available as properties of the model.
 *
 * @property string $id
 * @property string $name
 *
 * @property IdeaMember[] $ideaMembers
 */
abstract class BaseMembertype extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'membertype';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Member type|Member types', $n);
	}

	public static function representingColumn() {
		return 'name';
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>64),
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'ideaMembers' => array(self::HAS_MANY, 'IdeaMember', 'type_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'name' => Yii::t('app', 'Name'),
			'ideaMembers' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}