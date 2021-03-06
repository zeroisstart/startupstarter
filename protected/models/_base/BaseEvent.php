<?php

/**
 * This is the model base class for the table "event".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Event".
 *
 * Columns in table "event" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $id
 * @property string $title
 * @property string $start
 * @property string $end
 * @property integer $all_day
 * @property string $content
 * @property string $link
 * @property string $location
 * @property string $source
 * @property string $color
 * @property string $city
 * @property string $country
 *
 */
abstract class BaseEvent extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'event';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Event|Events', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('title, start', 'required'),
      array('all_day', 'default', 'value' => 0, 'setOnEmpty' => true, 'on' => 'insert'),
			array('all_day', 'numerical', 'integerOnly'=>true),
			array('title, link, location', 'length', 'max'=>255),
			array('content', 'length', 'max'=>1500),
			array('source, city, country', 'length', 'max'=>128),
			array('color', 'length', 'max'=>50),
			array('end', 'safe'),
			array('end, content, link, location, source, color, city, country', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, title, start, end, all_day, content, link, location, source, color, city, country', 'safe', 'on'=>'search'),
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
			'title' => Yii::t('app', 'Title'),
			'start' => Yii::t('app', 'Start'),
			'end' => Yii::t('app', 'End'),
			'all_day' => Yii::t('app', 'All Day'),
			'content' => Yii::t('app', 'Content'),
			'link' => Yii::t('app', 'Link'),
			'location' => Yii::t('app', 'Location'),
			'source' => Yii::t('app', 'Source'),
			'color' => Yii::t('app', 'Color'),
			'city' => Yii::t('app', 'City'),
			'country' => Yii::t('app', 'Country'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('start', $this->start, true);
		$criteria->compare('end', $this->end, true);
		$criteria->compare('all_day', $this->all_day);
		$criteria->compare('content', $this->content, true);
		$criteria->compare('link', $this->link, true);
		$criteria->compare('location', $this->location, true);
		$criteria->compare('source', $this->source, true);
		$criteria->compare('color', $this->color, true);
		$criteria->compare('city', $this->city, true);
		$criteria->compare('country', $this->country, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}