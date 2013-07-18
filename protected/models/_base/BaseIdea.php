<?php

/**
 * This is the model base class for the table "idea".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Idea".
 *
 * Columns in table "idea" available as properties of the model,
 * followed by relations of table "idea" available as properties of the model.
 *
 * @property string $id
 * @property string $time_registered
 * @property string $time_updated
 * @property integer $status_id
 * @property string $website
 * @property string $video_link
 * @property integer $deleted
 *
 * @property ClickIdea[] $clickIdeas
 * @property IdeaStatus $status
 * @property IdeaMember[] $ideaMembers
 * @property IdeaTranslation[] $ideaTranslations
 */
abstract class BaseIdea extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'idea';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Project|Projects', $n);
	}

	public static function representingColumn() {
		return 'id';
	}

	public function rules() {
		return array(
			array('status_id', 'required'),
			array('time_updated', 'required', 'on'=>'clicks'),
			array('status_id, deleted', 'numerical', 'integerOnly'=>true),
			array('website, video_link', 'length', 'max'=>128),
			array('time_updated', 'safe'),
			array('time_updated, website, video_link', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, time_registered, time_updated, status_id, website, video_link, deleted', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'clickIdeas' => array(self::HAS_MANY, 'ClickIdea', 'idea_click_id'),
			'status' => array(self::BELONGS_TO, 'IdeaStatus', 'status_id'),
			'ideaMembers' => array(self::HAS_MANY, 'IdeaMember', 'idea_id'),
			'ideaTranslations' => array(self::HAS_MANY, 'IdeaTranslation', 'idea_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'time_registered' => Yii::t('app', 'Registered'),
			'time_updated' => Yii::t('app', 'Last updated'),
			'status_id' => null,
			'website' => Yii::t('app', 'Project website'),
			'video_link' => Yii::t('app', 'Video Link'),
			'deleted' => Yii::t('app', 'Deleted'),
			'clickIdeas' => null,
			'status' => null,
			'ideaMembers' => null,
			'ideaTranslations' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('time_registered', $this->time_registered, true);
		$criteria->compare('time_updated', $this->time_updated, true);
		$criteria->compare('status_id', $this->status_id);
		$criteria->compare('website', $this->website, true);
		$criteria->compare('video_link', $this->video_link, true);
		$criteria->compare('deleted', $this->deleted);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}