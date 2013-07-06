<?php defined('KOOWA') or die('Restricted access');?>

<script data-inline src="http://connect.facebook.net/en_US/all.js"></script>
<script data-inline src="media://com_invites/js/facebook.js"></script>

<div id="fb-root"></div>


<?php
    $url = @route()->getURl(KHttpUrl::SCHEME | KHttpUrl::HOST | KHttpUrl::PORT );    
?>
<script>

<?php
$subject = htmlspecialchars(sprintf(@text('COM-INVITES-MESSAGE-SUBJECT'), JFactory::getConfig()->getValue('sitename')));
$body    = @helper('text.script', sprintf(@text('COM-INVITES-MESSAGE-BODY'), @name($viewer, false), JFactory::getConfig()->getValue('sitename')));
?>

new FacebookInvite({
	'appId'    : <?= $adapter->getApp()->id?>,
    'subject'  : '<?= $subject ?>',
    'body'     : '<?= $body?>',
    'link'     : '<?= $url?>',
    'picture'  : '<?= $viewer->getPortraitURL() ?>',
});

</script>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

	
<div class="an-entities-wrapper">	
	<?= @template('list') ?>
</div>



