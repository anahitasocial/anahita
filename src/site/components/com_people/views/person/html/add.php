<?php defined('KOOWA') or die; ?>
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
<form data-behavior="FormValidator" action="<?= @route('view=person') ?>" method="post" name="userform" id="userform" autocomplete="off">

	<div class="control-group">
		<label class="control-label"  for="username">
		<?php print JText::_( 'COM-ACTORS-NAME' ); ?>:
	    </label>
	    <div class="controls">
	    	<div class="input-prepend">
	    		<span class="add-on"><i class="icon-user"></i></span>
	    		<input data-validators="required" type="text" id="name" name="name" value="<?= @$name ?>" maxlength="25" />
	    	</div>
	    </div>
	</div>
	
	<div class="control-group">
		<label class="control-label"  for="username">
		<?php print JText::_('COM-PEOPLE-USERNAME'); ?>:
	    </label>
	    <div class="controls">
	    	<div class="input-prepend">
	    		<span class="add-on"><i class="icon-user"></i></span>
	    		<input data-validators="required validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="username" name="username" value="<?= @$username ?>" maxlength="25" />
	    	</div>
	    </div>
	</div>
	        
	<div class="control-group">
		<label class="control-label"  for="email">
			<?php print JText::_('COM-PEOPLE-EMAIL'); ?>:
		</label>
	    <div class="controls">
	    	<div class="input-prepend">
	    		<span class="add-on"><i class="icon-envelope"></i></span>
	    		<input data-validators="required validate-email validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="email" name="email" value="<?= @$email ?>" maxlength="100" />
	    	</div>
	    </div>
	</div>
	        
	<div class="control-group">
		<label class="control-label"  for="password">
	    	<?php print JText::_('COM-PEOPLE-PASSWORD'); ?>:
	    </label>
	    <div class="controls">
	    	<div class="input-prepend">
	    		<span class="add-on"><i class="icon-lock"></i></span>
	    		<?= @helper('password.input')?>	    		
	    	</div>
	    </div>
	</div>
	        
	
	<?php if ( !empty($return) ) : ?>
		<input type="hidden" name="return" value="<?= $return; ?>" />
	<?php endif;?>	
	        
	<div class="form-actions">
		<button type="submit" class="btn"><?= @text('COM-PEOPLE-ACTION-REGISTER') ?></button>
	</div>
        
</form>