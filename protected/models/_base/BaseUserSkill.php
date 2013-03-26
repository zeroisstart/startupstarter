<?php

/**
 * This is the model base class for the table "user_skill".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "UserSkill".
 *
 * Columns in table "user_skill" available as properties of the model,
 * followed by relations of table "user_skill" available as properties of the model.
 *
 * @property string $id
 * @property string $user_id
 * @property integer $skillset_id
 * @property integer $skill_id
 *
 * @property UserShare $user
 * @property Skillset $skillset
 * @property Skill $skill
 */
abstract class BaseUserSkill extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'user_skill';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'UserSkill|UserSkills', $n);
	}

	public static function representingColumn() {
		return 'id';
	}

	public function rules() {
		return array(
			array('match_id, skillset_id, skill_id', 'required'),
			array('skillset_id, skill_id', 'numerical', 'integerOnly'=>true),
			array('match_id', 'length', 'max'=>8),
			array('id, match_id, skillset_id, skill_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'match' => array(self::BELONGS_TO, 'UserMatch', 'match_id'),
			'skillset' => array(self::BELONGS_TO, 'Skillset', 'skillset_id'),
			'skill' => array(self::BELONGS_TO, 'Skill', 'skill_id'),
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
			'skillset_id' => null,
			'skill_id' => null,
			'match' => null,
			'skillset' => null,
			'skill' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('match_id', $this->match_id);
		$criteria->compare('skillset_id', $this->skillset_id);
		$criteria->compare('skill_id', $this->skill_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}