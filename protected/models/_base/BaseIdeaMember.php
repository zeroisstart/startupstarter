<?php

/**
 * This is the model base class for the table "idea_member".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "IdeaMember".
 *
 * Columns in table "idea_member" available as properties of the model,
 * followed by relations of table "idea_member" available as properties of the model.
 *
 * @property string $id
 * @property string $idea_id
 * @property string $user_id
 * @property integer $type
 *
 * @property UserShare $user
 * @property Idea $idea
 */
abstract class BaseIdeaMember extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'idea_member';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'IdeaMember|IdeaMembers', $n);
	}

	public static function representingColumn() {
		return 'id';
	}

	public function rules() {
		return array(
			array('idea_id, match_id, type', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('idea_id', 'length', 'max'=>9),
			array('match_id', 'length', 'max'=>8),
			array('id, idea_id, match_id, type', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'match' => array(self::BELONGS_TO, 'UserMatch', 'match_id'),
			'idea' => array(self::BELONGS_TO, 'Idea', 'idea_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'idea_id' => null,
			'match_id' => null,
			'type' => Yii::t('app', 'Type'),
			'match' => null,
			'idea' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('idea_id', $this->idea_id);
		$criteria->compare('match_id', $this->match_id);
		$criteria->compare('type', $this->type);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}