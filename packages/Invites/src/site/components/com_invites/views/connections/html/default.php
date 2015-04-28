<?php defined('KOOWA') or die('Restricted access');?>

<?php if(isset($service)) : ?>
<script data-inline src="http://connect.facebook.net/en_US/all.js"></script>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="media://com_invites/js/facebook.js" />
<?php else: ?>
<script src="media://com_invites/js/min/facebook.min.js" />
<?php endif; ?>

<div id="fb-root"></div>

<script>
<?php
$subject = htmlspecialchars(sprintf(@text('COM-INVITES-MESSAGE-SUBJECT'), JFactory::getConfig()->getValue('sitename')));
$body = @helper('text.script', sprintf(@text('COM-INVITES-MESSAGE-BODY'), @name($viewer, false), JFactory::getConfig()->getValue('sitename')));
$url = @route()->getUrl(KHttpUrl::SCHEME | KHttpUrl::HOST | KHttpUrl::PORT );
?>
$('body').invitesFacebook({
	
	'appId' :  <?= $service->getAppID() ?>,
    'subject' : '<?= $subject ?>',
    'body' : '<?= $body ?>',
    'appURL' : '<?= $url ?>',
    'picture' : '<?= $viewer->getPortraitURL() ?>'
});

</script>

<?= $url ?>

<?= @helper('ui.header', array()) ?>

<a href="#" data-trigger="Invite" class="btn btn-primary">
	+ <?= @text('COM-INVITES-ACTION-FB-INVITE') ?>
</a>

<div class="an-entities masonry">	
<?php 
$controller = @service('com://site/people.controller.person', array('request'=>array('view'=>'people')));                
$controller->getState()->setList($items);
?>
<?= $controller->getView()->layout('list')->display() ?>
</div>
<?php else: ?>
<?= @template('add') ?>
<?php endif; ?>




