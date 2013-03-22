<?php

/**
 * This is the model base class for the table "user_collabpref".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "UserCollabpref".
 *
 * Columns in table "user_collabpref" available as properties of the model,
 * followed by relations of table "user_collabpref" available as properties of the model.
 *
 * @property string $id
 * @property string $user_id
 * @property integer $collab_id
 *
 * @property UserShare $user
 * @property Collabpref $collab
 */
abstract class BaseUserCollabpref extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'user_collabpref';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'UserCollabpref|UserCollabprefs', $n);
	}

	public static function representingColumn() {
		return 'id';
	}

	public function rules() {
		return array(
			array('match_id, collab_id', 'required'),
			array('match_id', 'numerical', 'integerOnly'=>true),
			array('match_id', 'length', 'max'=>8),
			array('id, match_id, collab_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'match' => array(self::BELONGS_TO, 'UserMatch', 'match_id'),
			'collab' => array(self::BELONGS_TO, 'Collabpref', 'collab_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'match_id' => null,
			'collab_id' => null,
			'match' => null,
			'collab' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('match_id', $this->match_id);
		$criteria->compare('collab_id', $this->collab_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}