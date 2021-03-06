<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->pageTitle = Yii::t('app','Apply for event - "Sestavi svojo ekipo"');
?>
<?php echo CHtml::beginForm('','post',array("class"=>"custom large-7")); ?>


  <p>
    <?php echo CHtml::label(Yii::t('app','Do you wish to')." *",false); ?>
    <label for="p1">
    <?php echo CHtml::radioButton('Event[present]',(isset($_POST['Event']['present']) && ($_POST['Event']['present'] == 'Pitch your idea/project')),array("value"=>"Pitch your idea/project","id"=>"p1"))." ".Yii::t('app','Pitch your idea/project'); ?>
    </label>
    <label for="p2">
    <?php echo CHtml::radioButton('Event[present]',(isset($_POST['Event']['present']) && ($_POST['Event']['present'] == 'Join interesting idea/project')),array("value"=>"Join interesting idea/project","id"=>"p2"))." ".Yii::t('app','Join interesting idea/project');  ?>
    </label>
    <br />
    <?php echo CHtml::label(Yii::t('app','Have you ever been a cofounder?')." *",false); ?>
    <label for="c1">
    <?php echo CHtml::radioButton('Event[cofounder]',(isset($_POST['Event']['cofounder']) && ($_POST['Event']['cofounder'] == 'yes')),array("value"=>"yes","id"=>"c1"))." ".Yii::t('app','Yes'); ?>
    </label>
    <label for="c2">
    <?php echo CHtml::radioButton('Event[cofounder]',(isset($_POST['Event']['cofounder']) && ($_POST['Event']['cofounder'] == 'no')),array("value"=>"no","id"=>"c2"))." ".Yii::t('app','No');  ?>
    </label>
 </p>
 
<br />

<?php echo CHtml::submitButton(Yii::t('app',"Apply now"),array("class"=>"button radius success")); ?>
<?php echo CHtml::endForm(); ?>