 <?php if(Yii::app()->user->hasFlash('personalMessage')){ ?>
    <div data-alert class="alert-box radius success">
      <?php echo Yii::app()->user->getFlash('personalMessage'); ?>
      <a href="#" class="close">&times;</a>
    </div>
    <?php } ?>    

   <?php echo CHtml::beginForm('','post',array('class'=>"custom formidea")); ?>


    <?php echo CHtml::errorSummary($idea,"<div data-alert class='alert-box radius alert'>",'</div>'); ?>
    <?php echo CHtml::errorSummary($translation,"<div data-alert class='alert-box radius alert'>",'</div>'); ?>

    <?php echo CHtml::activeLabelEx($translation,'title'); ?>
    <span class="description">
      <?php echo Yii::t('msg','What are you calling it? One or two words please, you can always change it later.'); ?>
    </span>
    <?php echo CHtml::activeTextField($translation,"title", array('maxlength' => 128)); ?>

    <?php echo CHtml::activeLabelEx($translation,'pitch'); ?>
    <span class="description">
        <?php echo Yii::t('msg','This is your pitch. Be short and to the point.'); ?>
    </span>
    <?php echo CHtml::activeTextArea($translation,"pitch"); ?>

      <br />
    <?php echo CHtml::activeLabelEx($idea,'status_id'); ?>
     <span class="description">
      <?php echo Yii::t('msg','Status of project.'); ?>
     </span>  
    <?php echo CHtml::activedropDownList($idea, 'status_id', GxHtml::listData(IdeaStatus::model()->findAllTranslated(),'id','name'), array('empty' => '&nbsp;', 'style' => 'display: none;')); ?>



    <?php echo CHtml::activeLabelEx($translation,'description'); ?>
    
     <span class="description">
       <?php echo Yii::t('msg','Describe your project in detail.'); ?>
     </span>
    <?php echo CHtml::activeTextArea($translation,"description",array('class'=>'lin-edit')); ?> 
     <br />
    <?php echo CHtml::activeLabelEx($translation,'description_public'); ?>
    <div class="switch small round small-3" style="text-align: center;">
      <input id="description_public_0" name="IdeaTranslation[description_public]" type="radio" value="0" <?php if (!$translation->description_public) echo 'checked="checked"' ?>>
      <label for="description_public_0" onclick=""><?php echo Yii::t('msg','Off'); ?></label>

      <input id="description_public_1" name="IdeaTranslation[description_public]" type="radio" value="1" <?php if ($translation->description_public) echo 'checked="checked"' ?>>
      <label for="description_public_1" onclick=""><?php echo Yii::t('msg','On'); ?></label>
      <span></span>
   </div>
     
  <div class="lin-trigger panel">
    <?php echo CHtml::activeLabelEx($translation,'keywords'); ?>
    <div class="lin-hidden">
     <span class="description">
      <?php echo Yii::t('msg','Describe your project with comma separated keywords to increase visibility of your project.'); ?>
     </span>
    <?php echo CHtml::activeTextArea($translation,"keywords",array('class'=>'lin-edit')); ?>
    </div>
  </div>
     
  <div class="lin-trigger panel">
    <?php echo CHtml::activeLabelEx($translation,'tweetpitch'); ?>
    <div class="lin-hidden">
     <span class="description">
      <?php echo Yii::t('msg','Describe your project with comma separated keywords to increase visibility of your project.'); ?>
     </span>
    <?php echo CHtml::activeTextArea($translation,"tweetpitch",array('class'=>'lin-edit')); ?>
    </div>
  </div>      

  <div class="lin-trigger panel">
    <?php echo CHtml::activeLabelEx($idea,'website'); ?>
    <div class="lin-hidden">
    <?php echo CHtml::activeTextField($idea,"website", array('maxlength' => 128,'class'=>'lin-edit')); ?> 
    </div>
  </div>

  <div class="lin-trigger panel">
    <?php echo CHtml::activeLabelEx($idea,'video_link'); ?>
    <div class="lin-hidden">
    <?php echo CHtml::activeTextField($idea,"video_link", array('maxlength' => 128,'class'=>'lin-edit')); ?> 
    </div>
  </div>
      
    
<hr>
    <?php echo CHtml::submitButton(Yii::t("app","Save"),
          array('class'=>"button small success radius right")
      ); ?>
    <?php echo CHtml::endForm(); ?>  
