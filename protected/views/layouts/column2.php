
<?php $this->beginContent('//layouts/main'); ?>
<div class="row">
  <div class="small-12 large-10 push-2 columns" >
    <?php echo $content; ?>
  </div>
  <div class="small-12 large-2 pull-10 columns" style="padding-top:30px;">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'side-nav'),
		));
		$this->endWidget();
	?>
  </div>
</div>

<?php $this->endContent(); ?>