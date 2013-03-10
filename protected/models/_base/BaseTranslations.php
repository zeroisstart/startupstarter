<?php

/**
 * This is the model base class for the table "translations".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Translations".
 *
 * Columns in table "translations" available as properties of the model,
 * followed by relations of table "translations" available as properties of the model.
 *
 * @property string $ID
 * @property string $language_code
 * @property string $table
 * @property string $row_id
 * @property string $translation
 *
 * @property Languages $languageCode
 */
abstract class BaseTranslations extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'translations';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Translations|Translations', $n);
	}

	public static function representingColumn() {
		return 'table';
	}

	public function rules() {
		return array(
			array('language_code, table, row_id, translation', 'required'),
			array('language_code', 'length', 'max'=>2),
			array('table', 'length', 'max'=>64),
			array('row_id', 'length', 'max'=>10),
			array('translation', 'length', 'max'=>128),
			array('ID, language_code, table, row_id, translation', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'languageCode' => array(self::BELONGS_TO, 'Languages', 'language_code'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'ID' => Yii::t('app', 'ID'),
			'language_code' => null,
			'table' => Yii::t('app', 'Table'),
			'row_id' => Yii::t('app', 'Row'),
			'translation' => Yii::t('app', 'Translation'),
			'languageCode' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('ID', $this->ID, true);
		$criteria->compare('language_code', $this->language_code);
		$criteria->compare('table', $this->table, true);
		$criteria->compare('row_id', $this->row_id, true);
		$criteria->compare('translation', $this->translation, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}