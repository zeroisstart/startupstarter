<div  class="row">
	<h1 class="meta-title"><?php echo CHtml::encode(Yii::t('app','Recent users')); ?></h1>
	
	<?php if ($users){ ?>
	<div class="list-holder">
  <ul class="small-block-grid-1 large-block-grid-3 list-items">
		<?php 
		//$i = 0;
		//$page = 1;
		//$maxPage = 3;
		
		foreach ($users as $user){ ?>
			<li>
			<?php  $this->renderPartial('_user', array('user' => $user));  ?>
			</li>
		<?php } ?>
  </ul>
	</div>
	
	<div class="pagination-centered">
		
		<ul class="pagination">
			<?php if ($page > 1){ ?>
			<li class="arrow"><a href="<?php echo Yii::app()->createUrl("person/recent",array("id"=>$page-1)); ?>">&laquo;</a></li>
			<?php }else{ ?>
			<li class="arrow unavailable"><a>&laquo;</a></li>
			<?php } ?>
			
			<?php 
			  for ($i=1; $i <= $maxPage; $i++){
					if ($i == $page){ ?><li class="current"><?php }else{ ?><li><?php } ?>
					
					<a href="<?php echo Yii::app()->createUrl("person/recent",array("id"=>$i)); ?>"><?php echo $i; ?></a>
					</li>
			<?php	} ?>
					 
			
			<?php if ($page < $maxPage){ ?>
			<li class="arrow"><a href="<?php echo Yii::app()->createUrl("person/recent",array("id"=>$page+1)); ?>">&raquo;</a></li>
			<?php }else{ ?>
			<li class="arrow unavailable"><a>&raquo;</a></li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	
</div>