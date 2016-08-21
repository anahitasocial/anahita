<? defined('KOOWA') or die('Restricted access');?>

<? if (isset($service)) : ?>
<script data-inline src="https://connect.facebook.net/en_US/all.js"></script>

<? if (defined('JDEBUG') && JDEBUG) : ?>
<script src="media://com_invites/js/facebook.js" />
<? else: ?>
<script src="media://com_invites/js/min/facebook.min.js" />
<? endif; ?>

<div id="fb-root"></div>

<script>
<?
$subject = htmlspecialchars(sprintf(@text('COM-INVITES-MESSAGE-SUBJECT'), JFactory::getConfig()->getValue('sitename')));
$body = @helper('text.script', sprintf(@text('COM-INVITES-MESSAGE-BODY'), @name($viewer, false), JFactory::getConfig()->getValue('sitename')));
$url = @route()->getUrl(KHttpUrl::SCHEME | KHttpUrl::HOST | KHttpUrl::PORT);
?>
$('body').invitesFacebook({
		'appId' :  <?= $service->getAppID() ?>,
		'subject' : '<?= $subject ?>',
  	'body' : '<?= $body ?>',
  	'appURL' : '<?= $url ?>',
  	'picture' : '<?= $viewer->getPortraitURL() ?>'
});
</script>

<?= @helper('ui.header') ?>

<a href="#" data-trigger="Invite" class="btn btn-primary">
	+ <?= @text('COM-INVITES-ACTION-FB-INVITE') ?>
</a>

<div class="an-entities masonry">
<?
$controller = @service('com:people.controller.person', array('request' => array('view' => 'people')));
$controller->getState()->setList($items);
?>
<?= $controller->getView()->layout('list')->display() ?>
</div>
<? else: ?>
<?= @template('add') ?>
<? endif; ?>