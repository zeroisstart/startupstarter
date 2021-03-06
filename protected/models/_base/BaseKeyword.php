<?php

/**
 * This is the model base class for the table "translation".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Translation".
 *
 * Columns in table "translation" available as properties of the model,
 * followed by relations of table "translation" available as properties of the model.
 *
 * @property string $id
 * @property integer $language_id
 * @property string $table
 * @property string $row_id
 * @property string $translation
 *
 * @property Language $language
 */
abstract class BaseKeyword extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'keyword';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Keyword|Keywords', $n);
	}

	public static function representingColumn() {
		return 'keyword';
	}

	public function rules() {
		return array(
			array('language_id, table, row_id, keyword', 'required'),
			array('language_id', 'numerical', 'integerOnly'=>true),
			array('table', 'length', 'max'=>64),
			array('row_id', 'length', 'max'=>10),
			array('keyword', 'length', 'max'=>128),
			array('id, language_id, table, row_id, keyword', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'type' => Yii::t('app', 'Type'),
			'language_id' => Yii::t('app', 'Language'),
			'table' => Yii::t('app', 'Table'),
			'row_id' => Yii::t('app', 'Row'),
			'keyword' => Yii::t('app', 'Keyword'),
			'language' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('language_id', $this->language_id);
		$criteria->compare('table', $this->table, true);
		$criteria->compare('row_id', $this->row_id, true);
		$criteria->compare('keyword', $this->translation, true);
		$criteria->compare('type', $this->type, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}