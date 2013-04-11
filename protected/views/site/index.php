  
<?php /* ?><div id="intro1" style="background-position: 40% 0px; padding-bottom: 1px; padding-top: 30px;"><?php */ ?>
<div class="intro">
  <div  class="row" >
    <div class="large-12 small-12 columns" style="text-align: center;" >

      <h1>With the <span>right team</span> any <span>idea</span> can change your life</h1>

      <div class="row">
        <div class="large-6 large-centered small-12 columns">
          <p>
            <?php echo CHtml::encode(Yii::t('app','We are a group of enthusiasts on a mission to help anyone with a great idea to assemble a successful startup team capable of creating a viable business. We are developing a web platform through which you will be able to share your ideas with the same-minded entrepreneurs and search for interesting projects to join.')); ?>
          </p>
        </div>
      </div>

      <a href="#" class="close" data-tooltip title="<?php echo CHtml::encode(Yii::t('app','Hide intro')); ?>" onclick="$('.intro').slideUp('slow');"> &#x25B2; </a>

    </div>
  </div>
</div>


<div class="row panel radius" style="margin-top: 20px;">
	<div class="large-12 small-12 columns">
    <form class="custom">
      
		 <div class="row">
		  <div class="large-5 small-12 columns">
    		<h4 class="meta-title"><?php echo CHtml::encode(Yii::t('app','Narow your search')); ?>: </h4>
      </div>
		  <div class="large-7 small-12 columns">
    		
      <div class="row collapse">
        <div class="small-9 large-10 columns">
          <input type="text" class="radius" placeholder="<?php echo CHtml::encode(Yii::t('app','search by keywords')); ?>">
        </div>
        <div class="small-3 large-2 columns">
          <a href="#" class="button postfix radius"><?php echo CHtml::encode(Yii::t('app','search')); ?></a>
        </div>
      </div>
        
      </div>
		</div>
      
      <hr>
		 <div class="row">
		  <div class="large-3 small-6 columns">
		    <label>search by keywords:</label>
		    <input type="text" placeholder="keywords">
		  </div>
		  <div class="large-3 small-6 columns">
		    <label>search by skills:</label>
		    <input type="text" placeholder="skills">
		  </div>
		 <div class="large-3 small-6 columns">
       
      <label for="customDropdown1">Search by something</label>
      <select id="customDropdown1" class="medium">
        <option>This is a dropdown</option>
        <option>This is another option</option>
        <option>This is another option too</option>
        <option>Look, a third option</option>
      </select>
       
		</div>
			<div class="large-3 small-6 columns">
			<label for="photos"><input type="checkbox" style="display: none;" id="has-photos"><span class="custom checkbox"></span> Photos (44)</label>
			<label for="video"><input type="checkbox" style="display: none;" id="has-video" checked=""><span class="custom checkbox checked"></span> Videos (34)</label>
			<label for="detailed_description"><input type="checkbox" style="display: none;" checked="" id="has-description"><span class="custom checkbox checked"></span> Detailed Description (53)</label>
			<label for="attachment"><input type="checkbox" style="display: none;" id="has-attachment" checked=""><span class="custom checkbox checked"></span> Attachments (34)</label>			
			</div>      
		</div>
      
    </form>
    
		
	</div>
</div>



<?php if (isset($data['user'])){ ?>

<div  class="row">
  <ul class="small-block-grid-1 large-block-grid-3">
    
<?php foreach ($data['user'] as $user){ ?>    
  <li>
	<div class="large-12 small-12 columns radius panel card-person">
    <div class="row card-person-title" onclick="location.href='<?php echo Yii::app()->createUrl("person/".$user['id']); ?>'">
      <div class="large-12 small-12 columns" >
        <img src="<?php echo avatar_image(Yii::app()->user->getState('avatar_link'),Yii::app()->user->id); ?>" style="height:60px; margin-right: 10px; float:left;" />
        <h5><?php echo $user['name']." ".$user['surname']; ?></h5>
        Location: <a>Ljubljana, Slovenia</a>
		  </div>
	  </div>

    <div  class="row">
      <div class="large-12 small-12 columns"  >
        Has skills: <span class="button tiny secondary" data-tooltip title="C++</br>JavaScrip</br>PHP">Programming</span> <span class="button tiny secondary" data-tooltip title="Sales">Economics</span><br />
        Colaboration: <a>monetary, cofinder</a><br />
        Available: <a>part time</a><br />
        Involved in <a>3 projects</a>
        <div class="card-floater">
          <a href="<?php echo Yii::app()->createUrl("person/".$user['id']); ?>"><?php echo Yii::t('app','details...') ?></a>
        </div>
		  </div>
	  </div>
    
  </div>
    </li>
<?php } ?>
    </ul>
</div>

<?php } ?>

  
<?php if (isset($data['idea'])){ ?>

<div  class="row">
  <ul class="small-block-grid-1 large-block-grid-3">
<?php foreach ($data['idea'] as $idea){ ?>    
  
	 <li>
    <div class="large-12 small-12 columns radius panel card-idea">
    <div class="row card-idea-title" onclick="location.href='<?php echo Yii::app()->createUrl("idea/".$idea['id']); ?>'">
      <div class="large-12 small-12 columns" >
        <h5><?php echo $idea['title']; ?></h5>
        <?php echo Yii::t('app','Stage:') ?> <a><?php echo $idea['status']; ?></a>
        <div class="card-floater">
          &hearts;
        </div>
		  </div>
	  </div>

    <div  class="row">
      <div class="large-12 small-12 columns"  >
        <p>
          <?php echo $idea['pitch']; ?>
        </p>
        <p>
          <?php echo Yii::t('app','Looking for skills:'); ?> 
          <span class="button tiny secondary" data-tooltip title="C++</br>JavaScrip</br>PHP">Programming</span> 
          <span class="button tiny secondary" data-tooltip title="Sales">Economics</span>
        </p>
        <p>
          <?php 
          $i = 0;
          // show first 4 members
          foreach ($idea['member'] as $member){
            $i++; if ($i > 4) break;
          ?>
            <a href="<?php echo Yii::app()->createUrl("person/".$member['id']); ?>">
              <img src="<?php echo avatar_image($member['avatar_link'],$member['id']); ?>" data-tooltip title="<?php echo $member['name']." ".$member['surname']; ?>" alt="<?php echo $member['name']." ".$member['surname']; ?>" class="card-avatar" />
            </a>
          <?php } 
            // extra members
            if (count($idea['member']) > 4) echo "+".(count($idea['member'])-4);
          ?>
          <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/dummy-avatar-1.png" data-tooltip title="Has image" alt="Has image" class="card-icons" />
          <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/dummy-avatar-1.png" data-tooltip title="Has file" alt="Has file" class="card-icons" />
        </p>
        <hr>
        <?php echo Yii::t('app','Updated {n} day ago|Updated {n} days ago',array(1)); ?>
        <div class="card-floater">
          <a href="<?php echo Yii::app()->createUrl("idea/".$idea['id']); ?>"><?php echo Yii::t('app','details...') ?></a>
        </div>
		  </div>
	  </div>
    
  </div>
  </li>
  
<?php } ?>
</ul>
</div>
  
<?php } ?>

  
  <div class="pagination-centered">
  <ul class="pagination">
    <li class="arrow unavailable"><a href="">&laquo;</a></li>
    <li class="current"><a href="">1</a></li>
    <li><a href="">2</a></li>
    <li><a href="">3</a></li>
    <li><a href="">4</a></li>
    <li class="unavailable"><a href="">&hellip;</a></li>
    <li><a href="">12</a></li>
    <li><a href="">13</a></li>
    <li class="arrow"><a href="">&raquo;</a></li>
  </ul>
</div>

<div class="row panel radius">
	<div class="large-12 small-12 columns">

<h3>Recent ideas (looking for candidates)</h3>
<?php print_r($data['idea']); ?>

<h3>Recently registered users</h3>
<?php print_r($data['user']); ?>

<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
		
	</div>
</div>
