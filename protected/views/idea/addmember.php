<h1><?php echo Yii::t('app', 'Create') . ' ' . GxHtml::encode($idea->label()); ?></h1>

<?php
$this->renderPartial('_formaddmember', array(
		'idea' => $idea,
		'member' => $member,
		'buttons' => 'create'));
?>