<script>
  var skillSuggest_url = '<?php echo Yii::app()->createUrl("profile/sugestSkill",array("ajax"=>1)) ?>';
</script>

<div class="row createidea">
  <div class="columns edit-header">
    <h3>
      <?php echo Yii::t('app', 'Project presentation'); ?>
    </h3>
  </div>
  <div class="columns panel edit-content">
    <?php
      $this->renderPartial('_formideaedit', array(
          'id' => $id,
          'lang' => $lang,
          'idea' => $idea,
          'language' => $language,
          'translation' => $translation,
          'buttons' => 'create'));
    ?>
  </div>
</div>

<div class="row createidea">
  <div class="columns edit-header">
    <h3>
      <?php echo Yii::t('app', 'Team'); ?>
    </h3>
  </div>
  <div class="columns panel edit-content">
    <?php
      $this->renderPartial('_formmembersedit', array(
          'id' => $id,
          'lang' => $lang,
          'ideadata' => $ideadata,
          'idea_id' => $idea_id));
    ?>
  </div>
</div>


<div class="row">
  <div class="columns edit-header">
    <div class="edit-floater">
      <?php if(!isset($candidate)){ ?>
      <a class="small button radius" style="margin-bottom:0;" href="<?php echo Yii::app()->createUrl('project/edit',array('id'=>$id,'lang'=>$lang,'candidate'=>'new')); ?>">
        <?php echo Yii::t('app','Add new') ?>
        <span class="icon-plus"></span>
      </a>
        <?php } ?>
    </div>
    
     <h3><?php if(!isset($candidate)){ echo Yii::t('app', 'Open positions'); }
              else echo Yii::t('app', 'New positions');?>
    </h3>
    
  </div>
  <div class="columns panel edit-content">    
    
  <?php if(isset($candidate) && isset($match)){
      $this->renderPartial('_formteamedit', array(
          'id' => $id,
          'lang' => $lang,
          'ideadata' => $ideadata,
          'candidate' => $candidate,
          'match' => $match,
          'buttons' => 'create'));
  } else {
      $this->renderPartial('_formteamedit', array(
          'id' => $id,
          'lang' => $lang,
          'ideadata' => $ideadata,
          'buttons' => 'create'));
  }?>
    
  
  <?php
  
  if (!isset($_GET['candidate'])){
    
  echo "<hr>".CHtml::submitButton(Yii::t("app","Finish"),
            array('class'=>"button small success radius",
                'onclick'=>'window.location.href=(\''.$idea_id.'\');')
        );
  
   } ?>
    
</div>
</div>    

<div class="row createidea">
  <div class="columns edit-header">
    <h3>
      <?php echo Yii::t('app', 'Spread the word'); ?>
    </h3>
  </div>
  <div class="columns panel edit-content">
    <?php
      $this->renderPartial('_formsocialedit', array(
          'id' => $id,
          'lang' => $lang,
          'translation' => $translation,
          'idea_id' => $idea_id,
          'buttons' => 'create'));
    ?>
  </div>
</div>
