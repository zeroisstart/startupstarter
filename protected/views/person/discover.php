<?php
	$this->pageTitle = 'Find talent'; // leave this empty
	$this->pageDesc = '';

  $baseUrl = Yii::app()->baseUrl; 
  $cs = Yii::app()->getClientScript();
  
  $cs->registerCssFile($baseUrl.'/css/ui/jquery-ui-1.10.3.custom.min.css');
  $cs->registerScriptFile($baseUrl.'/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);
?>

<script>
	var skillSuggest_url = '<?php echo Yii::app()->createUrl("site/sugestSkill",array("ajax"=>1)) ?>';
	var citySuggest_url = '<?php echo Yii::app()->createUrl("site/sugestCity",array("ajax"=>1)) ?>';
	var countrySuggest_url = '<?php echo Yii::app()->createUrl("site/sugestCountry",array("ajax"=>1)) ?>';
</script>

<div id="drop-local-project" class="f-dropdown content small" data-dropdown-content>
  <div class="invitation-form">

      <?php echo CHtml::label(Yii::t('app','City'),'message'); ?>
      <div class="row collapse">
        <div class="small-9 columns">
          <?php echo CHtml::textField('search_local','',array('class'=>'city')); ?>
        </div>
        <div class="small-3 columns">
           <?php echo CHtml::button(Yii::t("app","Find"),array("class"=>"search_local_button postfix button radius",
                  'onclick'=>"location.href='".Yii::app()->createUrl("person/discover")."?SearchForm[city]='+$('#search_local').val();")); ?>
        </div>
      </div>    
  </div>
</div>

<div class="row pannel radius" style="margin-top: 40px;">
  <div class="columns">
    <h4><?php echo Yii::t('app','Find'); ?></h4>
    <a href="<?php echo Yii::app()->createUrl("person/discover",array('SearchForm[collabPref]'=>'3')); ?>" class="button round success" style="margin-left:20px;"><?php echo Yii::t("app","Cofounder"); ?></a>
    <a href="<?php echo Yii::app()->createUrl("person/discover",array('SearchForm[collabPref]'=>'4')); ?>" class="button round success" style="margin-left:20px;"><?php echo Yii::t("app","Investors"); ?></a>
    <a href="<?php echo Yii::app()->createUrl("person/discover",array('SearchForm[available]'=>'8')); ?>" class="button round success" style="margin-left:20px;"><?php echo Yii::t("app","Weekend hackers"); ?></a>
    <a href="<?php echo Yii::app()->createUrl("person/discover",array('SearchForm[available]'=>'40')); ?>" class="button round success" style="margin-left:20px;"><?php echo Yii::t("app","Workoholics"); ?></a>
    <a href="#" data-dropdown="drop-local-project" onclick="$('#search_local').focus()" class="button round success" style="margin-left:20px;"><?php echo Yii::t("app","Local people"); ?></a>
  </div>
</div>


<div class="row panel searchpanel radius" style="margin-top: 40px;">
	<div class="columns search_content edit-header">
    <a class="anchor-link" id="filter_search"></a>
    
		<div class="row">
		  <div class="large-3 columns">
    		<h4 class="meta-title"><?php echo Yii::t('app','Sort your search by'); ?> </h4>
      </div>
		  <div class="large-9 columns">
				
			<a class="exp_srch button small secondary right round" href="#" onclick="$('.advance').toggle(); return false;"><?php echo Yii::t('app','Advanced search'); ?> <span class="icon-caret-down"></span></a>
        
      </div>
		</div>
		
    <?php echo CHtml::beginForm(Yii::app()->createUrl("person/discover")."#filter_search",'get',array('class'=>"custom","style"=>"margin-bottom:0;")); ?>
		
		<div class="row filter_people" <?php if ($filter->isProject) echo 'style="display:none"'; ?>>
			<div class="large-3 columns">
				<?php //echo CHtml::activeTextField($filter,"colabPref"); ?>
				
				<?php echo CHtml::label(Yii::t('app','Collaboration'),''); ?>
				<?php echo CHtml::dropDownList('SearchForm[collabPref]',$filter->collabPref, 
              //GxHtml::listDataEx(Language::model()->findAllAttributes(null, true))
              CHtml::listData(Collabpref::model()->findAllTranslated(),"id","name")
							, array('empty' => '&nbsp;',"class"=>"large-3","style"=>"display:none")); ?>
				
				
			</div>
			<div class="large-3 columns">
				<?php //echo CHtml::activeTextField($filter,"colabPref"); ?>
				
				<?php echo CHtml::label(Yii::t('app','Availability'),''); ?>
				<?php echo CHtml::dropDownList('SearchForm[available]',$filter->available, 
              //GxHtml::listDataEx(Language::model()->findAllAttributes(null, true))
              CHtml::listData(Available::model()->findAllTranslated(),"id","name")
							, array('empty' => '&nbsp;',"class"=>"large-3","style"=>"display:none")); ?>
			</div>
			<?php /* ?>
			<div class="large-3 columns">
				<?php //echo CHtml::activeTextField($filter,"colabPref"); ?>
				
				<?php echo CHtml::label(Yii::t('app','Country'),''); ?>
				<?php echo CHtml::dropDownList('SearchForm[country]','', 
              //GxHtml::listDataEx(Language::model()->findAllAttributes(null, true))
              CHtml::listData(Country::model()->findAll(),"id","name")
							, array('empty' => '&nbsp;',"class"=>"large-3","style"=>"display:none")); ?>
			</div><?php */ ?>
			<div class="large-3 columns">
				<label><?php echo Yii::t('app','Country'); ?></label>
				<?php echo CHtml::textField('SearchForm[country]',$filter->country,array("class"=>"country")); ?>
			</div>
			
			
			<div class="large-3 columns"  style="text-align: center;  padding-top: 16px;">
					<?php echo CHtml::submitButton(Yii::t("app","Search"),
							array('class'=>"button small radius")
					); ?>
				 &nbsp; 
				 <a href="<?php echo Yii::app()->createUrl("person/discover"); ?>" >
					<?php echo CHtml::button(Yii::t("app","Reset"),
								array('class'=>"button small radius secondary")
						); ?>
	    </div>
			
			<div class="advance" <?php if (!$filter->checkAdvanceForm()) echo "style='display:none'"; ?>>
		      <hr>

					<div class="large-3 columns">
						<label><?php echo Yii::t('app','City'); ?></label>
						<?php echo CHtml::textField('SearchForm[city]',$filter->city,array("class"=>"city")); ?>
					</div>

					<div class="large-9 columns">
						<label><?php echo Yii::t('app','Skill'); ?></label>
						<?php echo CHtml::textField('SearchForm[skill]',$filter->skill,array("class"=>"skill")); ?>
					</div>
			</div>
		</div>
      
    <?php echo CHtml::endForm(); ?>
	
	</div>
</div>
  




<div class="row" id="recent_projects">
	<?php
	if (count($searchResult) && count($searchResult['data'])){
		Yii::log(arrayLog($searchResult), CLogger::LEVEL_INFO, 'custom.info.search_result'); 
		?>
  
    <div class="hide-for-medium-down">
      <div class="page-navigation">
        <ul>
          <li><a href="#page1"><?php echo Yii::t("app", "Page"); ?> 1</a></li>
        </ul>
      </div>
    </div>

		<div class="list-holder">
      
      <div class="list-items">
        <a id="page<?php echo $searchResult['page']; ?>" class="anchor-link"></a>
        
        <h5><?php echo Yii::t("app","Page")." ".$searchResult['page']; ?></h5>
        <ul class="small-block-grid-1 large-block-grid-3">
          <?php 
          foreach ($searchResult['data'] as $result){ ?>
            <li>
            <?php $this->renderPartial('//person/_user', array('user' => $result)); ?>
            </li>
          <?php } ?>
        </ul>
      </div>
		</div>

		<div class="pagination-centered">
			<?php $this->widget('ext.Pagination.WPagination',array("url"=>"person/discover","page"=>$searchResult['page'],"maxPage"=>$searchResult['maxPage'],"getParams"=>$_GET)); ?>
		</div>
	<?php }else{	?>
	
	<h3><?php echo Yii::t('msg','No results found with this filters.') ?></h3>
	
	<?php } ?>
</div>	
