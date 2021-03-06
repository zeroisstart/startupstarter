<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
'id',
array(
			'name' => 'language',
			'type' => 'raw',
			'value' => $model->language !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->language)), array('backendLanguage/view', 'id' => GxActiveRecord::extractPkValue($model->language, true))) : null,
			),
array(
			'name' => 'idea',
			'type' => 'raw',
			'value' => $model->idea !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->idea)), array('backendIdea/view', 'id' => GxActiveRecord::extractPkValue($model->idea, true))) : null,
			),
'pitch',
'description',
'description_public:boolean',
'tweetpitch',
'deleted',
'title',
	),
)); ?>

