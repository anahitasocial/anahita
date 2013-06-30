<?php defined('KOOWA') or die('Restricted access');?>


<div id="fb-root"></div>

<script data-inline src="media://com_invites/js/facebook.js"></script>
<script>

<?php
$subject = htmlspecialchars(sprintf(@text('COM-INVITES-MESSAGE-SUBJECT'), JFactory::getConfig()->getValue('sitename')));
$body    = @helper('text.script', sprintf(@text('COM-INVITES-MESSAGE-BODY'), @name($viewer, false), JFactory::getConfig()->getValue('sitename')));
?>
new FacebookInvite({
	'appId'    : '131488453564721',
    'subject'  : '<?= $subject ?>',
    'body'     : '<?= $body?>',
    'link'     : 'http://www.anahitapolis.com/',
    'picture'  : '<?= $viewer->getPortraitURL() ?>',
    'appURL'   : 'http://www.anahitapolis.com/'
});

</script>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

	
<div class="an-entities-wrapper">	
	<?= @template('list') ?>
</div>



