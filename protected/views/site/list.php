<?php
	$this->pageTitle = Yii::t('app','Admin panel');
?>

<p>Tukaj naj bo seznam vseh povezav do avto zgeneriranih CRUDov:</p><br />

<p>
<a href="<?php echo Yii::app()->createUrl("backendAuditTrail"); ?>">Action Trail</a><br />
<a href="<?php echo Yii::app()->createUrl("site/contact", array("q"=>"test")); ?>">Contact</a><br />
<a href="<?php echo Yii::app()->createUrl("newsletter"); ?>">Newsletter</a><br />
<a href="<?php echo Yii::app()->createUrl("backendUser/inactive"); ?>">Neaktivirani uporabniki</a><br />
<a href="<?php echo Yii::app()->createUrl("statistic"); ?>">Statistika</a><br />
</p>

<br />
Šifranti:<br/>
<a href="<?php echo Yii::app()->createUrl("backendCity"); ?>">Cities</a><br />
<a href="<?php echo Yii::app()->createUrl("backendClickIdea"); ?>">Clicked Ideas</a><br />
<a href="<?php echo Yii::app()->createUrl("backendClickUser"); ?>">Clicked Users</a><br />
<a href="<?php echo Yii::app()->createUrl("backendCollabpref"); ?>">Collab prefs</a><br />
<a href="<?php echo Yii::app()->createUrl("backendCountry"); ?>">Countries</a><br />
<a href="<?php echo Yii::app()->createUrl("backendIdeaStatus"); ?>">IdeasStatuses</a><br />
<a href="<?php echo Yii::app()->createUrl("backendLanguage"); ?>">Languages</a><br />
<a href="<?php echo Yii::app()->createUrl("backendSkill"); ?>">Skills</a><br />

<br />

<a href="<?php echo Yii::app()->createUrl("backendTranslation"); ?>">Translations</a><br />

<br />
Users:<br />
<a href="<?php echo Yii::app()->createUrl("backendUser"); ?>">Users</a><br />
<a href="<?php echo Yii::app()->createUrl("backendUserLink"); ?>">User Links</a><br />
<a href="<?php echo Yii::app()->createUrl("backendUserMatch"); ?>">User Matches</a><br />
<a href="<?php echo Yii::app()->createUrl("backendUserSkill"); ?>">Users Skills</a><br />
<a href="<?php echo Yii::app()->createUrl("backendUserCollabpref"); ?>">Users Collabprefs</a><br />
<a href="<?php echo Yii::app()->createUrl("backendInvite"); ?>">Invites</a><br />

<br />
Ideje:<br/>
<a href="<?php echo Yii::app()->createUrl("backendIdea"); ?>">Ideas</a><br />
<a href="<?php echo Yii::app()->createUrl("backendIdeaTranslation"); ?>">Ideas Translations</a><br />
<a href="<?php echo Yii::app()->createUrl("backendIdeaMember"); ?>">Ideas Members</a><br />

<br />
Events:<br/>
<a href="<?php echo Yii::app()->createUrl("backendEvent"); ?>">Event</a><br />
<a href="<?php echo Yii::app()->createUrl("backendEventCofinder"); ?>">Event Cofinder</a><br />
<a href="<?php echo Yii::app()->createUrl("backendEventSignup"); ?>">Event Signup</a><br />

<br/>

<a href="<?php echo Yii::app()->createUrl("site/recalcPerc"); ?>">Calculate percentage</a><br />
