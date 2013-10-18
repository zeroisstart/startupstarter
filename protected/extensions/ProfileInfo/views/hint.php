<?php if (Yii::app()->user->hasFlash('WProfileInfoHint')){
  $hintAction = Yii::app()->user->getFlash('WProfileInfoHint');
  $hint = substr($hintAction, 0, strpos($hintAction, "|"));
  $action = substr($hintAction, strpos($hintAction, "|")+1);
  ?>
  <div data-alert class="alert-box radius">
    <?php echo $hint; ?>
    <a href="<?php echo $action; ?>" class="action" style="margin-bottom: 0;"><?php echo Yii::t("app",'Do it now!') ?> <span class="icon-long-arrow-right"></span></a>
    <a href="#" class="close"><span class="icon-remove-sign"></span></a>
  </div>
<?php } ?>