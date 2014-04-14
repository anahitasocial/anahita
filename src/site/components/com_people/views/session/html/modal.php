<?php defined('KOOWA') or die('Restricted access') ?>
    
<popup:header>
	<h3>
		<?= @text('COM-PEOPLE-SESSION-TITLE') ?> 
	</h3>
</popup:header>


<?php KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service') ?>
<?php if ( class_exists('ComConnectTemplateHelperService', true) ): ?>
<p><?= KService::get('com://site/connect.template.helper.service')->renderLogins() ?></p>
<hr/>
<?php endif; ?>

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
	
	<div class="control-group">
		<label class="checkbox">
			<input type="checkbox" name="remember" value="true" alt="<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>" />
			<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>
		</label>
	</div>
	
	<?php if ( !empty($return) ) : ?>
		<input type="hidden" name="return" value="<?= $return; ?>" />
	<?php endif;?>
</form>

<popup:footer>
	<?php if ( @service('com://site/people.controller.person')->permission->canRegister() ) : ?>
	<p class="lead pull-left">
		<a href="<?= @route('option=com_people&view=person&layout=add'.( !empty($return) ? '&return='.$return : '')) ?>">
		    <?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT')?>
		</a>
	</p>
	<?php endif;?>

	<button data-behavior="<?= isset($ajax) ? 'Request' : 'Submit'?>" data-request-form="#modal-login-form" data-submit-form="#modal-login-form" data-request-redirect="true" name="Submit" class="btn btn-large btn-primary">
    		<?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
    </button>
</popup:footer>


