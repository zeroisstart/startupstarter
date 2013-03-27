<?php

/**
 * This is the model base class for the table "language".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Language".
 *
 * Columns in table "language" available as properties of the model,
 * followed by relations of table "language" available as properties of the model.
 *
 * @property integer $id
 * @property string $language_code
 * @property string $name
 *
 */
abstract class BaseLanguage extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'language';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Language|Languages', $n);
	}

	public static function representingColumn() {
		return 'name';
	}

	public function rules() {
		return array(
			array('language_code, name', 'required'),
			array('language_code', 'length', 'max'=>2),
			array('name', 'length', 'max'=>32),
			array('id, language_code, name', 'safe', 'on'=>'search'),
		);
	}

/*	public function relations() {
		return array(
			'ideaTranslations' => array(self::HAS_MANY, 'IdeaTranslation', 'language_id'),
			'translations' => array(self::HAS_MANY, 'Translation', 'language_id'),
			'userTmps' => array(self::HAS_MANY, 'UserTmp', 'language_id'),
		);
	}*/

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'language_code' => Yii::t('app', 'Language Code'),
			'name' => Yii::t('app', 'Name'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('language_code', $this->language_code, true);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}