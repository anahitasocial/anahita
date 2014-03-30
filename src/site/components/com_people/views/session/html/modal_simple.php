<?php defined('KOOWA') or die('Restricted access') ?>
    
<popup:header>
	<a href="#" class="close">x</a>
	<h3><?= @text('COM-PEOPLE-SESSION-TITLE') ?></h3>
</popup:header>

<div id="flash-message"></div>

<form id="modal-login-form" action="<?= @route() ?>" method="post">
	<div class="control-group">			
		<div class="controls">
			<input class="input-block-level" name="username" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-USERNAME-EMAIL')?>" id="username" type="text" alt="username" size="18" />
		</div>
	</div>
	
	<div class="control-group">				
		<div class="controls">
			<input class="input-block-level" type="password" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-PASSWORD')?>" id="passwd" name="password" size="18" alt="password" /> 
			<a href="<?= @route('view=token') ?>">
			<?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
			</a>
		</div>
	</div>

	<?php if ( !empty($return) ) : ?>
		<input type="hidden" name="return" value="<?= $return; ?>" />
	<?php endif;?>
</form>

<popup:footer>
    <button data-behavior="<?= isset($ajax) ? 'Request' : 'Submit' ?>" data-request-form="#modal-login-form" data-submit-form="#modal-login-form" name="Submit" class="btn btn-large btn-primary">
    	<?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
    </button>    
</popup:footer>
