<?php defined('KOOWA') or die('Restricted access');?>

<script data-inline src="http://connect.facebook.net/en_US/all.js"></script>

<div id="fb-root"></div>
<script data-inline>
// assume we are already logged in
FB.init({appId: '131488453564721', xfbml: true, cookie: true});

Delegator.register('click', {

	'Invite' : function(event, el, api) {
		event.stop();
		
		<?php 
		$siteConfig = JFactory::getConfig(); 
		$messageSubject = htmlspecialchars(sprintf(@text('COM-INVITES-MESSAGE-SUBJECT'), $siteConfig->getValue('sitename')));
		$messageBody = @helper('text.script', sprintf(@text('COM-INVITES-MESSAGE-BODY'), @name($viewer, false), $siteConfig->getValue('sitename')));
		?>

		new Request.JSON({
			url: '<?= @route('option=com_invites&view=token&format=json') ?>',
			onSuccess: function(token)
			{				
				FB.ui({
					display: 'iframe',
					method:	'send',
					name: 'Anahita',
					//link: token.url,
					//redirect_uri: 'localhost/anahita/releases/2.2/site/',
					link: 'http://www.anahitapolis.com/',
					picture: '<?= $viewer->getPortraitURL() ?>',
					redirect_url: 'http://www.anahitapolis.com/index.php?option=com_invites&view=token',
					to: api.get('id'),
					name: '<?= $messageSubject ?>',
					description: "<?= $messageBody ?>"
					},
					function(response){
						if(response.success){
							new Request.HTML({
									method: 'post',
									url: 'index.php?option=com_invites&view=facebook',
									data: 'action=invite&token=' + token.value
								}).send();
						}	
					}.bind(this)	
				);
			}
		}).get();
	}
});
</script>

<?php $invitables = $adapter->getInvitables(); ?>
<div id="an-actors" class="an-entities masonry" data-behavior="InfinitScroll" data-infinitscroll-options="{'numColumns':6, 'limit':<?= $limit ?>}">
	<?php foreach($invitables as $invitable): ?>
	<div class="an-entity">
		<div class="entity-actions">
			<a class="btn" href="#" data-trigger="Invite" data-invite-options="{'id': <?= $invitable->id ?> }"><?= @text('COM-INVITES-ACTION-INVITE') ?></a>
		</div>
	
		<div class="entity-portrait-medium">
			<img src="<?= $invitable->thumb_avatar ?>?type=large" >
		</div>
		
		<h3 class="entity-title">
			<?= $invitable->name ?>
		</h3>
	</div>
	<?php endforeach; ?>
</div>


