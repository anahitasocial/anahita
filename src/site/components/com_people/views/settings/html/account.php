<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-PEOPLE-PROFILE-EDIT-ACCOUNT-INFORMATION') ?></h3>
<?php $user = $item->getJUserObject() ?>
<form data-behavior="FormValidator" action="<?= @route('view=person&id='.$item->id) ?>" method="post" name="userform" id="userform" autocomplete="off">

		<div class="control-group">
		<label class="control-label"  for="username">
		<?php print JText::_( 'COM-ACTORS-NAME' ); ?>:
	    </label>
	    <div class="controls">
	    	<div class="input-prepend">
	    		<span class="add-on"><i class="icon-user"></i></span>
	    		<input data-validators="required" type="text" id="name" name="name" value="<?= $item->name?>" maxlength="25" />
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
	    		<input data-validators="required validate-username validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="username" name="username" value="<?= $item->username ?>" maxlength="25" />
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
	    		<input data-validators="required validate-email validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="email" name="email" value="<?= $item->email ?>" maxlength="100" />
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
	    		<?= @helper('password.input', false)?>	    		
	    	</div>
	    </div>
	</div>

	
	<?php        
		$user   = JFactory::getUser($item->userId);
	    $params = $user->getParameters(true)->renderToArray();        
	?>                
	
	<div class="control-group">
		<label class="control-label" for="timezone">
			<?php print JText::_( 'Time Zone' ); ?>:
		</label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class="icon-globe"></i></span>
				<?= $params['timezone'][1] ?>
			</div>
		</div>
	</div>
	        
	<div class="form-actions">
		<button type="submit" class="btn" ><?php print @text('LIB-AN-ACTION-SAVE'); ?></button>
	</div>
    
</form>