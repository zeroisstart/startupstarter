<?php

/**
 * This is the model base class for the table "user_tag".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "UserTag".
 *
 * Columns in table "user_tag" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $id
 * @property string $user_id
 * @property string $tag
 * @property string $applied_at
 * @prošerty string $content
 *
 */
abstract class BaseUserTag extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'user_tag';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'UserTag|UserTags', $n);
	}

	public static function representingColumn() {
		return 'tag';
	}

	public function rules() {
		return array(
			array('user_id, tag', 'required'),
			array('user_id', 'length', 'max'=>10),
			array('tag, content', 'length', 'max'=>255),
      array('applied_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
			array('id, user_id, tag, applied_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'user_id' => Yii::t('app', 'User'),
			'tag' => Yii::t('app', 'Tag'),
      'applied_at' => '',
      'content' => '',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('user_id', $this->user_id, true);
		$criteria->compare('tag', $this->tag, true);
    $criteria->compare('applied_at', $this->applied_at, true);
    $criteria->compare('content', $this->content, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}