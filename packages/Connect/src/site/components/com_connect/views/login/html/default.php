<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="none"></module>

<?php $alert_title = sprintf(@text('COM-CONNECT-LOGIN-PROMPT-HI'), $api->getUser()->name, ucfirst($api->getName())) ?>

<?php if ( @service('com://site/people.controller.person')->canRegister() ) : ?>
<div class="alert alert-block">
	<h4><?= $alert_title ?></h4>
	<p><?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN-OR-SIGNUP') ?></p>
</div>
<div class="well">    
    <button data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=session&layout=modal_simple') ?>" class="btn btn-large"><?= @text('COM-CONNECT-ACTION-MAP-ACCOUNT')?></button>
    <button data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=person&layout=add&modal=1') ?>" class="btn btn-large"><?= @text('COM-CONNECT-ACTION-SIGNUP')?></button>
</div>
<?php else : ?>
<div class="alert alert-block">
<h4><?= $alert_title ?></h4>
<p><?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN')?></p>
</div>

<div class="well"> 
	<button data-request-redirect="true" data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=session&layout=modal_simple') ?>" class="btn btn-large btn-primary">
		<?= @text('COM-CONNECT-LOGIN-PROMPT-LOGIN')?>
	</button>
</div>
<?php endif;?>
