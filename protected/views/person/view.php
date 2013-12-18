<?php
/* @var $this SiteController */
$this->pageTitle = "";
$user = $data['user'];
?>
<div id="drop-msg" class="f-dropdown content medium" data-dropdown-content>
	<div class="contact-form">
	<?php
	if (Yii::app()->user->isGuest) echo Yii::t('msg','You must be loged in to contact this person.'); 
	else { ?>    
	<?php echo CHtml::beginForm(Yii::app()->createUrl("message/contact"),'post',array("class"=>"customs")); ?>
      <?php echo CHtml::hiddenField("user",$user['id']); ?>
      <?php echo CHtml::label(Yii::t('app','Message').":",'message'); ?>
      <?php echo CHtml::textArea('message') ?>
      <br />
      
      <label for="notify_me">
        <?php echo CHtml::checkBox('notify_me',true); ?>
        <?php echo Yii::t('app','Send me a copy by email'); ?>
      </label>
      <br />
      <div class="login-floater">
      <?php echo CHtml::submitButton(Yii::t("app","Send"),array("class"=>"button small radius")); ?>
      </div>

  <?php echo CHtml::endForm();
	}
	?>
	</div>
</div>
<div class="row profile-wrap" style="margin-top:50px; padding-top: 35px; padding-bottom:60px;">


<div class="large-4 columns profile side">
	<div class="panel">
	<img class="th panel-avatar" src="<?php echo avatar_image($user['avatar_link'], $user['id'], false); ?>" />
		<br>
	
	

		<h1 class=""><?php echo $user['name'] . " " . $user['surname']; ?></h1>

	<div class="item">
	<p>
	<?php if ($user['city'] || $user['country'] /*|| $user['address']*/) { ?>
	<a><span class="" data-tooltip title="<img src='<?php echo getGMap($user['country'], $user['city'], $user['address']); ?>'>">
  <span class="icon-map-marker icon-awesome"></span>
	<?php if ($user['address']) echo $user['address']."<br />"; ?>
	<?php
	echo $user['city'];
	if ($user['city'] && $user['country']) echo ', ';
	echo $user['country'];
	?>
	<?php //echo $user['address'];  ?>
	</span>	</a>
	<?php } ?>
	</p>
	</div>
	<a class="button contact-me success large-12 small-12 radius" href="#" data-dropdown="drop-msg"><?php echo Yii::t('app', 'Contact me') ?></a>
	</div>


	<div class="panel">	
	
		<?php if ($user['available_name']) { ?>
		<h4 class="l-iblock"> <?php echo Yii::t('app', 'Available') ?></h4>
		<h1 style="margin-top:3px;" ><span class="icon-time" style="margin-right:10px;"></span><?php echo $user['available_name']; ?>
		<?php } ?></h1>
			
	
	</div>

	
	<!-- <p class="meta-field"><?php // echo Yii::t('app', 'My links') ?>:</p> -->
	<div class="panel">
		<div class="item">
      
	<?php if (count($user['link']) > 0) { ?>
		<h4 class=""> <?php echo Yii::t('app', 'Links') ?></h4>
	<?php 
	foreach ($user['link'] as $link) {
	?>
	
	<p><span class=""><a href="<?php echo add_http($link['url']); ?>" target="_blank">
	<img class="link-icon" src="<?php echo getLinkIcon($link['url']); ?>">
	<?php echo $link['title']; ?>  </a></span>
	</p>
	<?php 
	}
	?>
	<?php } ?>
	</div>

	
	<p>
	<h4><?php echo Yii::t('app', 'Registered') ?></h4>
	<span class=""><!-- <?php // echo Yii::t('app', 'Member since') ?>:  -->
	<?php echo Yii::app()->dateFormatter->formatDateTime(strtotime($user['create_at']), "long", null); ?></span>
	</p>
	</div>
</div>
  
  
<div class="large-8 right main">
<div class="skills large-12 columns"  >
		<div class="panel radius">
		<h3 class="edit-content-title">
		<?php	echo Yii::t('app', 'Skilled in'); ?>
		</h3>
		
		<?php
		if (isset($user['skillset'])){
		foreach ($user['skillset'] as $skillset){
      
  		echo "<div class='panel radius'><h4>".$skillset['skillset']."</h4> ";
      foreach ($skillset['skill'] as $skill){ ?>

      <span data-alert class="label radius profile-skills" id="skill_<?php echo $skill['id']; ?>">
      <?php echo $skill['skill'].""; ?>
      </span>

      <?php
      } ?>
		</div> 
		<?php } }else{ ?>
      <div class="description"><?php  echo Yii::t('msg','User doesn\'t have this filled out yet.');  ?></div>
    <?php } ?>
</div>


<?php

/*foreach ($user['skill'] as $skill) {
?>
</span>
<span class="button tiny secondary meta_tags" data-tooltip title="<?php echo $skill['skillset']; ?>"><?php echo $skill['skill']; ?></span>
<?php
}*/
?>

<!-- <hr> -->

</div>

<div class="large-12 columns  collaboration" >				
	<div class="panel radius">
		<?php if (count($user['collabpref']) > 0) { ?>
    <h3 class='edit-content-title'>
		<?php echo Yii::t('app', 'Collaboration'); ?>
    </h3>
		

      <?php
      $firsttime = true;
      if (is_array($user['collabpref'])){ ?>

				<?php
				foreach ($user['collabpref'] as $collab) {
				if (!$collab['active']) continue;
				if (!$firsttime) echo "";
				$firsttime = false;
        ?>
        
      <span class="label secondary radius">
				<?php echo $collab['name']; ?>
      </span>
      <?php } ?>

		<?php }else{ ?>
			<div class="description"><?php  echo Yii::t('msg','User doesn\'t have this filled out yet.');  ?></div>
  	<?php }
    } ?>	
		
	</div>
</div>
<?php if ($user['bio']){ ?>
<div class="large-12 columns about-me"  >
	<div class="panel radius">
		<h3 class="edit-content-title">
		<?php echo Yii::t('app','Something about me'); ?>
		</h3>
		
		<p class="meta-field">
      <?php echo $user['bio']; ?>
		</p>
	 </div>
</div>
<?php } ?>
<div class="large-12 columns"  >
	<div class="panel radius inside-panel">
		<!-- <hr> -->
		<?php if (count($user['idea']) > 0) { ?>
		<h3 class="edit-content-title">
		<?php echo Yii::t('app', 'Involved in {n} project|Involved in {n} projects', array(count($user['idea']))) ?>
		</h3>
		<p class="meta-field">
		<?php
		if (is_array($user['idea']))
      foreach ($user['idea'] as $idea_data) {
      ?><div class="idea-list radius panel"><a class="" href="<?php echo Yii::app()->createUrl("project/" . $idea_data['id']); ?>"><h5><?php echo $idea_data['title']; ?></h5></a>
        <div class="clearbox"></div>
      </div><?php 
      }
      ?>
		<?php } ?>
	</div>
</div> 
</div>
</div>

<?php
Yii::log(arrayLog($user), CLogger::LEVEL_INFO, 'custom.info.user');
