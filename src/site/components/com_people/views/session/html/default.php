<?php defined('KOOWA') or die('Restricted access') ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<div class="row">
	<div class="offset3 span6">	
		<form action="<?=@route()?>" method="post" class="well">
			<?php if ( !empty($return) ) : ?>
	    	<input type="hidden" name="return" value="<?= $return; ?>" />
	    	<?php endif;?>
		
			<?= @flash_message ?>
		
			<fieldset>
				<legend>
					<?= @text('COM-PEOPLE-SESSION-TITLE') ?>
					
					<?php if ( @service('com://site/people.controller.person')->permission->canRegister() ) : ?>
		     		<small>
		     			<a class="pull-right" href="<?= @route('option=com_people&view=person&layout=add'.(!empty($return) ? "&return=$return" : '')) ?>">
		       				<?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT')?>
		     			</a>
		     		</small>
		     		<?php endif;?>
				</legend>	         
	         
	         	<?php if ( KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service') ): ?>
				<p class="lead"><?= @text('COM-PEOPLE-SOCIALMEDIA-LOGIN') ?></p>
	         	<p><?= $this->renderHelper('com://site/connect.template.helper.service.renderLogins') ?></p>
				<hr/>
				<p class="lead"><?= @text('LIB-AN-OR') ?></p>
	         	<?php endif ?>
         
				<div class="control-group">			
					<div class="controls">
						<input class="input-block-level" name="username" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-USERNAME-EMAIL')?>" id="username" type="text" alt="username" size="18" />
					</div>
				</div>
				
				<div class="control-group">				
					<div class="controls">
						<input class="input-block-level" type="password" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-PASSWORD')?>" id="passwd" name="password" size="18" alt="password" />
						<a href="<?= @route('view=token') ?>"><?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?></a> 
					</div>
				</div>
				
				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="remember" value="true" alt="<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>" />
						<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>
					</label>
				</div>
			</fieldset>
			
			<div class="form-actions">
				<button type="submit" name="Submit" class="btn btn-large btn-primary pull-right"/>
					<?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
				</button>
			</div>
		</form>
	</div>
</div>