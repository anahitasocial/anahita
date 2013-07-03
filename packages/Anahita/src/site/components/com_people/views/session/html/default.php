<?php defined('KOOWA') or die('Restricted access') ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<div class="row">
	<div class="offset3 span6">	
		<form action="<?=@route()?>" method="post" class="well">
		<fieldset>
			<legend><?= @text('COM-PEOPLE-SESSION-TITLE') ?></legend>
			<?php if ( KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service') ): ?>
			<div class="connect-service-actions">
			<?= $this->renderHelper('com://site/connect.template.helper.service.renderLogins')?>			
			</div>
						           
            <?php endif ?>
         
            <?= @flash_message ?>
         
			<div class="control-group">			
				<div class="controls">
					<input class="input-block-level" name="username" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-USERNAME-EMAIL')?>" id="username" type="text" alt="username" size="18" />
				</div>
			</div>
			
			<div class="control-group">				
				<div class="controls">
					<input class="input-block-level" type="password" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-PASSWORD')?>" id="passwd" name="password" size="18" alt="password" />
				</div>
			</div>
			
			
			<div id="form-login-remember" class="control-group">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="yes" alt="<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>" />
					<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>
				</label>
			</div>
			
    		<?php if ( !empty($return) ) : ?>
    			<input type="hidden" name="return" value="<?= $return; ?>" />
    		<?php endif;?>
    					
			<div class="form-actions">
				<button type="submit" name="Submit" class="btn btn-large btn-primary"/>
					<?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
				</button>
			</div>
		</fieldset>

		<a href="<?= @route('view=token') ?>">
		<?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
		</a>
		
		<?php if ( !empty($this->return) ) : ?>
		<input type="hidden" name="return" value="<?= $this->return; ?>" />
		<?php endif;?>
		
		</form>
	</div>
</div>