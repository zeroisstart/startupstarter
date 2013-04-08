<?php

class backendUserLinkController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'UserLink'),
		));
	}

	public function actionCreate() {
		$model = new UserLink;


		if (isset($_POST['UserLink'])) {
			$model->setAttributes($_POST['UserLink']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'UserLink');


		if (isset($_POST['UserLink'])) {
			$model->setAttributes($_POST['UserLink']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'UserLink')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('UserLink');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new UserLink('search');
		$model->unsetAttributes();

		if (isset($_GET['UserLink']))
			$model->setAttributes($_GET['UserLink']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}