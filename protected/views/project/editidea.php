<div class="large-12 columns">
<?php
  $this->pageTitle=Yii::t('app','Edit project');

  $baseUrl = Yii::app()->baseUrl; 
  $cs = Yii::app()->getClientScript();
  
  $cs->registerCssFile($baseUrl.'/css/ui/jquery-ui-1.10.3.custom.min.css');
  $cs->registerScriptFile($baseUrl.'/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);
?>

<script>
  var skillSuggest_url = '<?php echo Yii::app()->createUrl("profile/suggestSkill",array("ajax"=>1)) ?>';
  var citySuggest_url = '<?php echo Yii::app()->createUrl("site/suggestCity",array("ajax"=>1)) ?>';
  var inviteMember_url = '<?php echo Yii::app()->createUrl("project/suggestMember",array("ajax"=>1)) ?>';  
</script>

<div class="row createidea">
  <div class="columns edit-header">
   
    
      
    <h3>
      <?php echo Yii::t('app', 'Project presentation'); ?>
    </h3>

    
    <a class="button small radius"  href="<?php echo Yii::app()->createUrl("project/translate",array("id"=>$id)); ?>"><?php echo Yii::t('app', 'Translate project'); ?></a>

      
      
    <hr>
    <div class="columns languages" style="margin-bottom: 10px;">
        <span style="float:left; margin-right: 8px; margin-top:5px;"><?php echo Yii::t('app','Languages'); ?>:</span> 
        <ul class="button-group radius left">
          <li><a class="button tiny"><?php echo $ideadata['language']; ?></a></li>
          <?php 
           if (count($ideadata['translation_other'])){ 
              foreach ($ideadata['translation_other'] as $trans){
                echo '<li><a href="?lang='.$trans['language_code'].'" class="button tiny secondary">'.$trans['language']."</a></li>";
              }
            
            }
           ?>
          <li><a class="button success tiny"  href="<?php echo Yii::app()->createUrl("project/translate",array("id"=>$id)); ?>"><?php echo Yii::t('app', 'New translation'); ?></a></li>
          </ul>
          
    </div>
      
     
  </div>

  <div class="columns panel edit-content">
    
   
    <?php if (count($ideadata['translation_other'])){ ?>
    <div class="edit-floater">
      
        <?php 
        echo CHtml::link(Yii::t("app","Delete this translation"),Yii::app()->createUrl('project/deleteTranslation',array('id'=>$idea['id'],'lang'=>$ideadata['language_code'])),
                  array('class'=>"button tiny alert radius",
                        'confirm'=>Yii::t("msg","You are about to delete this translation!")."\n".Yii::t("msg","Are you sure?"),
                        'onclick'=>"$(document).stopPropagation();",
                      )
              );
        ?>
    </div> 
    <br /><br />
    <?php } ?>
    
    <?php
      $this->renderPartial('_formideaedit', array(
          'id' => $id,
          'lang' => $lang,
          'idea' => $idea,
          'language' => $language,
          'translation' => $translation,
          'buttons' => 'create' ));
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
          'idea_id' => $idea_id,
          'invitees' => $invite['data'],
          'invitations' => $invite['count'],
          'isOwner'=>$isOwner));
    ?>
  </div>
</div>


<div class="row">
  <div class="columns edit-header">
    <a id="link_position" class="anchor-link"></a>
      
    <div class="edit-floater">
      <?php if(!isset($candidate)){ ?>
      <a class="small button radius" style="margin-bottom:0;" href="<?php echo Yii::app()->createUrl('project/edit',array('id'=>$id,'lang'=>$lang,'candidate'=>'new')); ?>#link_position">
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
    
</div>
</div>  
</div>  

<?php /* ?>
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
<?php */ ?>
