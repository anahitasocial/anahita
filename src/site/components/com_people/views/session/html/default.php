<?php defined('KOOWA') or die('Restricted access') ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<div class="row">
	<div class="offset3 span6">	
		<form action="<?=@route()?>" method="post">
		<fieldset>
			<legend><?= @text('COM-PEOPLE-SESSION-TITLE') ?></legend>
         
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
				<?php if ( KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service') ): ?>
				<?= $this->renderHelper('com://site/connect.template.helper.service.renderLogins')?>			         
            	<?php endif ?>
			
				<button type="submit" name="Submit" class="btn btn-large btn-primary pull-right"/>
					<?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
				</button>
			</div>
		</fieldset>

		<ul class="unstyled">
			<li>
				<a href="<?= @route('view=token') ?>">
					<?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
				</a>
			</li>
		
			<?php if ( @service('com://site/people.controller.person')->permission->canRegister() ) : ?>
     		<li>
     			<a href="" data-trigger="BS.showPopup" data-bs-showpopup-url="<?=@route('option=com_people&view=person&layout=add&modal=1'.(!empty($return) ? "&return=$return" : ''))?>">
       				<?= @text('COM-PEOPLE-ACTION-SIGNUP-NEW-ACCOUNT')?>
     			</a>
     		</li>
     		<?php endif;?>
		</ul>
		
		<?php if ( !empty($this->return) ) : ?>
		<input type="hidden" name="return" value="<?= $this->return; ?>" />
		<?php endif;?>
		
		</form>
	</div>
</div>