<?php defined('KOOWA') or die; ?>

<form id="registration-form" data-behavior="FormValidator" action="<?= @route('view=person') ?>" method="post" name="userform" id="userform" autocomplete="off">
	<?php if ( !empty($return) ) : ?>
	<input type="hidden" name="return" value="<?= $return; ?>" />
	<?php endif;?>

	<div class="control-group">
		<label class="control-label"  for="username">
			<?php print JText::_( 'COM-PEOPLE-ADD-NAME' ); ?>
	    </label>
	    <div class="controls">
	    	<input class="input-block-level" data-validators="required" type="text" id="name" name="name" value="" maxlength="25" />
	    </div>
	</div>
	
	<div class="control-group">
		<label class="control-label"  for="username">
			<?php print JText::_('COM-PEOPLE-ADD-USERNAME'); ?>
	    </label>
	    <div class="controls">
	    	<input class="input-block-level" data-validators="required validate-username validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="username" name="username" value="" maxlength="25" />
	    </div>
	</div>
	        
	<div class="control-group">
		<label class="control-label"  for="email">
			<?php print JText::_('COM-PEOPLE-ADD-EMAIL'); ?>
		</label>
	    <div class="controls">
	    	<input class="input-block-level" data-validators="required validate-email validate-remote url:'<?=@route('view=person', false)?>'" type="text" id="email" name="email" value="" maxlength="100" />
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
</form>

<popup:footer>
    <button data-behavior="Submit" data-submit-form="#registration-form" data-request-redirect="true" class="btn btn-large btn-primary">
    	<?= @text('COM-PEOPLE-ACTION-REGISTER') ?>
    </button>
</popup:footer>