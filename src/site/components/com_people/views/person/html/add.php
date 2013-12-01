<?php defined('KOOWA') or die; ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<div class="row">
	<div class="offset3 span6">	
        <?php 
        $user = @service('repos://site/users')->getQuery(true)->fetchValue('id');
        if ( !$user ) : 
        //first time user
        //must be an admin
        ?>
        <div class="alert alert-info alert-block">
        <h4><?= @text('COM-PEOPLE-WELCOME1') ?></h4>
        <?= @text('COM-PEOPLE-WELCOME2') ?>
        </div>
        <?php endif;?>	
		<form data-behavior="FormValidator" action="<?= @route('view=person') ?>" method="post" name="userform" id="userform" class="well" autocomplete="off">
			<?php if ( !empty($return) ) : ?>
			<input type="hidden" name="return" value="<?= $return; ?>" />
			<?php endif;?>
		
			<fieldset>
				<legend><?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT') ?></legend>
		
				<div class="control-group">
					<label class="control-label"  for="username">
						<?php print JText::_( 'COM-PEOPLE-ADD-NAME' ); ?>
				    </label>
				    <div class="controls">
				    	<input class="input-block-level" data-validators="required" type="text" id="name" name="name" value="<?= empty($name) ? '' : $name ?>" maxlength="25" />
				    </div>
				</div>
				
				<div class="control-group">
					<label class="control-label"  for="username">
						<?php print JText::_('COM-PEOPLE-ADD-USERNAME'); ?>
				    </label>
				    <div class="controls">
				    	<input class="input-block-level" data-validators="required validate-username validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="username" name="username" value="<?= empty($username) ? '' : $username ?>" maxlength="25" />
				    </div>
				</div>
				        
				<div class="control-group">
					<label class="control-label"  for="email">
						<?php print JText::_('COM-PEOPLE-ADD-EMAIL'); ?>
					</label>
				    <div class="controls">
				    	<input class="input-block-level" data-validators="required validate-email validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="email" name="email" value="<?= empty($email) ? '' : $email ?>" maxlength="100" />
				    </div>
				</div>
				        
				<div class="control-group">
					<label class="control-label"  for="password">
				    	<?php print JText::_('COM-PEOPLE-ADD-PASSWORD'); ?>
				    </label>
				    <div class="controls">
				    	<?= @helper('password.input') ?>	    		
				    </div>
				</div>
			</fieldset>
			        
			<div class="form-actions">
				<button type="submit" class="btn btn-primary btn-large pull-right">
					<?= @text('COM-PEOPLE-ACTION-REGISTER') ?>
				</button>
			</div>
		</form>
	</div>
</div>
